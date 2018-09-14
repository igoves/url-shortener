<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;

class UrlShortenerController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('app');
    }

    /**
     * Create shore URL.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'url' => 'required|url',
        ]);
        if ($validator->fails()) {
            $error_text = '';
            foreach ($validator->messages()->all('<li>:message</li>') as $message)
            {
                $error_text .= $message;
            }
            return response()->json([
                'status' => 0,
                'msg' => $error_text
            ]);
        }

        $check = DB::table('shorturls')->where('url', $request->url)->first();
        if ( $check ) {
            return response()->json([
                'status' => 1,
                'msg' => 'URL Generated',
                'url' => env('APP_URL') . '/' . $check->s_url,
            ]);
        }

        $id = DB::table('shorturls')->insertGetId(['url' => $request->url]);
        $short_url = $this->dec2any($id);
        DB::table('shorturls')->where('id', $id)->update(['s_url' => $short_url]);

        return response()->json([
            'status' => 1,
            'msg' => 'URL Generated',
            'url' => env('APP_URL') . '/' . $short_url,
        ]);
    }

    /**
     * Generate short mirror.
     *
     * @return \Illuminate\Http\Response
     */
    private function dec2any( $num, $base = 62, $index=false ) : string
    {
        if ( !$base ) {
            $base = \strlen( $index );
        } elseif (! $index ) {
            $index = substr( '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ', 0, $base );
        }
        $out = '';
        for ( $t = floor( log10( $num ) / log10( $base ) ); $t >= 0; $t-- ) {
            $a = floor( $num / ( $base ** $t ) );
            $out .= substr( $index, $a, 1 );
            $num -= ( $a * ( $base ** $t ) );
        }
        return $out;
    }


    /**
     * Redirect to full url
     *
     * @return \Illuminate\Http\Response
     */
    public function go($url)
    {
        $data = DB::table('shorturls')->where('s_url', $url)->first();
        if ( !$data ) abort(404, 'Not found');
        return Redirect::to($data->url);
    }

}
