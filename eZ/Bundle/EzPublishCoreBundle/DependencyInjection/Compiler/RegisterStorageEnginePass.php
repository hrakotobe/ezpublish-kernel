<?php
/**
 * File containing the RegisterStorageEnginePass class.
 *
 * @copyright Copyright (C) 1999-2014 eZ Systems AS. All rights reserved.
 * @license http://www.gnu.org/licenses/gpl-2.0.txt GNU General Public License v2
 * @version //autogentag//
 */

namespace eZ\Bundle\EzPublishCoreBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use LogicException;
use Symfony\Component\DependencyInjection\Reference;

/**
 * This compiler pass will register eZ Publish storage engines
 */
class RegisterStorageEnginePass implements CompilerPassInterface
{
    /**
     * Performs compiler passes for persistence factories
     *
     * Does:
     * - Registers all storage engines to ezpublish.api.storage_engine.factory
     *
     * @param \Symfony\Component\DependencyInjection\ContainerBuilder $container
     *
     * @throws \LogicException
     */
    public function process( ContainerBuilder $container )
    {
        if ( !$container->hasDefinition( 'ezpublish.api.storage_engine.factory' ) )
            return;

        $storageEngineFactoryDef = $container->getDefinition( 'ezpublish.api.storage_engine.factory' );
        foreach ( $container->findTaggedServiceIds( 'ezpublish.storageEngine' ) as $id => $attributes )
        {
            foreach ( $attributes as $attribute )
            {
                if ( !isset( $attribute['alias'] ) )
                    throw new LogicException( 'ezpublish.storageEngine service tag needs an "alias" attribute to identify the storage engine. None given.' );

                // Register the storage engine on the main storage engine factory
                $storageEngineFactoryDef->addMethodCall(
                    'registerStorageEngine',
                    array(
                        new Reference( $id ),
                        $attribute['alias']
                    )
                );
            }
        }
    }
}
