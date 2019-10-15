<?php

declare(strict_types=1);

/*
 * @copyright   2019 Mautic Inc. All rights reserved
 * @author      Mautic, Inc.
 *
 * @link        https://www.mautic.com
 *
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

namespace MauticPlugin\IntegrationsBundle\Sync\SyncDataExchange\Internal\Object;

interface ObjectInterface
{
    /**
     * Returns name key of the object.
     *
     * @return string
     */
    public function getName(): string;

    /**
     * Returns full Doctrine entity class name of the object.
     *
     * @return string
     */
    public function getEntityName(): string;
}
