<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\User;
use LaravelFCM\Message\PayloadNotificationBuilder;
use FCM;

class FirebaseController extends Controller
{
    // paquete utilizado para fcm composer require brozot/laravel-fcm
    // php artisan vendor:publish --provider="LaravelFCM\FCMServiceProvider"
    public function sendAll(Request $request){
        // dd($request->all());
        $recipients = User::whereNotNull('device_token')
            ->pluck('device_token')->toArray();
        
        $notificationBuilder = new PayloadNotificationBuilder($request->input('title'));
        $notificationBuilder->setBody($request->input('body'))
                                ->setSound('default');
        
        $notification = $notificationBuilder->build();
        
        $groupResponse = FCM::sendToGroup($recipients, null, $notification, null);
        
        $notification = "Notificación enviada a todos los usuarios (Android)";
        return back()->with(compact('notification'));
    }
}
