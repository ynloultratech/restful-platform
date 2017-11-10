<?php
/*
 * LICENSE: This file is subject to the terms and conditions defined in
 * file 'LICENSE', which is part of this source code package.
 *
 * @copyright 2017 Copyright(c) - All rights reserved.
 *
 * @author YNLO-Ultratech Development Team <developer@ynloultratech.com>
 * @package restful-platform
 */

namespace Ynlo\RestfulPlatformBundle\MediaServer;

use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PropertyAccess\PropertyAccessor;
use Symfony\Component\Routing\Router;

class LocalMediaStorageProvider extends AbstractMediaStorageProvider
{
    /**
     * @var Router
     */
    protected $router;

    /**
     * @var string
     */
    protected $secret;

    /**
     * LocalMediaStorageProvider constructor.
     *
     * @param Router $router
     * @param string $secret
     */
    public function __construct(Router $router, $secret)
    {
        $this->router = $router;
        $this->secret = $secret;
    }

    /**
     * @inheritDoc
     */
    public function read(MediaFileInterface $media)
    {
        $handle = fopen($this->getFileName($media), 'rb+');

        return fread($handle, filesize($this->getFileName($media)));
    }

    /**
     * @inheritDoc
     */
    public function getDownloadUrl(MediaFileInterface $media)
    {
        if (!$this->config['private']) {
            return rtrim($this->config['base_url'], '/').'/'.$media->getUuid().'/'.$media->getName();
        }

        $routeName = $this->config['route_name'];
        $route = $this->router->getRouteCollection()->get($routeName);
        preg_match_all('/{(\w+)}/', $route->getPath(), $matches);

        $accessor = new PropertyAccessor();
        $params = [];
        if (isset($matches[1])) {
            foreach ($matches[1] as $paramName) {
                $params[$paramName] = $accessor->getValue($media, $paramName);
            }
        }

        $params[$this->config['signature_parameter']] = $this->createSignature($media);

        return $this->router->generate($routeName, $params, Router::ABSOLUTE_URL);
    }

    /**
     * @inheritDoc
     */
    public function save(MediaFileInterface $media, string $content)
    {
        $fileSystem = new Filesystem();
        $fileSystem->mkdir($this->getDirName($media));
        $handle = fopen($this->getFileName($media), 'wb+');
        if ($this->config['private']) {
            $media->setStorageMeta(['salt' => md5(time().mt_rand())]);
        } else {
            $media->setStorageMeta([]);
        }

        fwrite($handle, $content);
        fclose($handle);
    }

    /**
     * @inheritDoc
     */
    public function remove(MediaFileInterface $media)
    {
        $fileSystem = new Filesystem();

        $fileSystem->remove($this->getDirName($media));
    }

    /**
     * @param MediaFileInterface $media
     *
     * @return string
     */
    public function getDirName(MediaFileInterface $media)
    {
        return $this->config['dir_name'].'/'.$media->getUuid();
    }

    /**
     * @param MediaFileInterface $media
     *
     * @return string
     */
    public function getFileName(MediaFileInterface $media)
    {
        return $this->getDirName($media).'/'.$media->getName();
    }

    /**
     * @param MediaFileInterface $media
     * @param string             $signature
     *
     * @return bool
     */
    public function isValidSignature(MediaFileInterface $media, $signature)
    {
        $data = unserialize(openssl_decrypt($signature, 'aes-128-cbc', $media->getStorageMetaValue('salt'), 0, $this->getIv()), []);

        return
            $data['uuid'] === $media->getUuid()
            && $data['name'] === $media->getName()
            && (new \DateTime($data['expire']))->getTimestamp() > time();
    }

    /**
     * @param MediaFileInterface $media
     * @param Request            $request
     *
     * @return bool
     */
    public function isValidSignedRequest(MediaFileInterface $media, Request $request)
    {
        $signature = $request->get($this->config['signature_parameter']);

        return $this->isValidSignature($media, $signature);
    }

    /**
     * @param MediaFileInterface $media
     *
     * @return string
     */
    public function createSignature(MediaFileInterface $media)
    {
        $maxAge = $this->config['signature_max_age'];
        $time = \DateTime::createFromFormat('U', time())->modify("+{$maxAge}Seconds");
        $data = [
            'uuid' => $media->getUuid(),
            'name' => $media->getName(),
            'expire' => $time->format('c'),
        ];

        $salt = $media->getStorageMetaValue('salt');

        return openssl_encrypt(serialize($data), 'aes-128-cbc', $salt, 0, $this->getIv());
    }

    /**
     * @return bool|string
     */
    private function getIv()
    {
        return substr(preg_replace('/[^\w]/', '', $this->secret), 0, 16);
    }
}