<?php

/*
 * This file is part of the tenancy/tenancy package.
 *
 * (c) Daniël Klabbers <daniel@klabbers.email>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @see http://laravel-tenancy.com
 * @see https://github.com/tenancy
 */

namespace Tenancy\Database;

use Illuminate\Contracts\Events\Dispatcher;
use Tenancy\Database\Contracts\ProvidesDatabaseDriver;
use Tenancy\Database\Contracts\ResolvesConnections;
use Tenancy\Identification\Contracts\IdentifiableAsTenant;

class ConnectionResolver implements ResolvesConnections
{
    /**
     * @var Dispatcher
     */
    protected $events;

    public function __construct(Dispatcher $events)
    {
        $this->events = $events;
    }

    public function __invoke(IdentifiableAsTenant $tenant): ?ProvidesDatabaseDriver
    {
        $provider = $this->events->until(new Events\Resolving($tenant));

        if ($provider) {
            $this->events->dispatch(new Events\Identified($tenant, $provider));
        }

        $this->events->dispatch(new Events\Resolved($tenant, $provider));

        return $provider;
    }
}
