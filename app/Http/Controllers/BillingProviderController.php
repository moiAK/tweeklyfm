<?php

/*
 * This file is part of tweeklyfm/tweeklyfm
 *
 *  (c) Scott Wilcox <scott@dor.ky>
 *
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 *
 */

namespace App\Http\Controllers;

use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
use Laravel\Cashier\WebhookController as BaseController;

class BillingProviderController extends BaseController
{
    public function handleCustomerCreated($payload)
    {
        // Log::info($payload);
    }

    // Override the default Cashier webhook controller so that we can set our own status flag
    protected function handleCustomerSubscriptionDeleted(array $payload)
    {
        $billable = $this->getBillable($payload['data']['object']['customer']);

        if ($billable && $billable->subscribed()) {
            $billable->subscription_active = false;
            $billable->subscription()->cancel();
            $billable->save();
        }

        return new Response('Webhook Handled', 200);
    }
}
