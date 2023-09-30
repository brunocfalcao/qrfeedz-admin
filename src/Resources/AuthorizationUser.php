<?php

namespace QRFeedz\Admin\Resources;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use QRFeedz\Admin\Traits\DefaultAscPKSorting;
use QRFeedz\Foundation\Abstracts\QRFeedzResource;

class AuthorizationUser extends QRFeedzResource
{
    use DefaultAscPKSorting;

    public static $model = \QRFeedz\Cube\Models\AuthorizationUser::class;

    public static $search = [
        'name',
    ];

    public function title()
    {
        return $this->name;
    }

    public function subtitle()
    {
        $total = DB::table('authorizables')
                   ->where('authorization_id', $this->id)
                   ->count();

        return $total.' '.Str::plural('entity', $total);
    }

    public function fields(Request $request)
    {
        return [
        ];
    }
}
