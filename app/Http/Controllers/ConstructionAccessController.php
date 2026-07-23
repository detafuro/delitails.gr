<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\Request;

class ConstructionAccessController extends Controller
{
    public function unlock(Request $request)
    {
        $request->validate(['passcode' => ['required', 'string', 'max:64']]);

        $expected = (string) Setting::get('under_construction_passcode', '');

        if ($expected === '' || ! hash_equals($expected, (string) $request->input('passcode'))) {
            return back()->withErrors(['passcode' => 'That passcode is not right.']);
        }

        $request->session()->put('construction_bypass', hash('sha256', $expected));

        return redirect('/');
    }
}
