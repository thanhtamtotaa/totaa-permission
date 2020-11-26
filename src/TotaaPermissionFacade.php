<?php

namespace Totaa\TotaaPermission;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Totaa\TotaaPermission\Skeleton\SkeletonClass
 */
class TotaaPermissionFacade extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'totaa-permission';
    }
}
