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

namespace Ynlo\RestfulPlatformBundle\Annotation;

/**
 * Link model definitions to related child definitions.
 * Should be used in a property of class using @ModelDefinition
 *
 * e.g.
 *
 * Model definition in the class:
 *
 * @ModelDefinition(name="UserInList", serializerGroups={"list"})
 * User {
 *  ...
 * }
 *
 * then use the DefinitionMap to map parent and child definitions
 *
 * DefinitionMap in some property
 *
 * @DefinitionMap({"UserInList":"ACLWithoutDetails"})
 * property $acl;
 *
 * In the above example when the "UserInList" definition is applied
 * the "ACLWithoutDetails" definition is applied automatically to the related ACL object
 *
 * NOTE: the mapped definition, e.g. ACLWithoutDetails, should be a valid definition in the other side
 *
 * @Annotation()
 * @Target("PROPERTY")
 */
final class DefinitionMap
{
    /**
     * Unique name to identify the definition
     *
     * NOTE: this name should be unique in the entire app
     *
     * @var array
     */
    public $map = [];
}
