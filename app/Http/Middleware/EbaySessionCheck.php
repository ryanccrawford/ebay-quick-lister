<?php

namespace App\Http\Middleware;

use Closure;

class EbaySessionCheck
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {

        $uri = $request->path();
        if ($uri === 'getauth' || $uri === 'oauth' || $uri === 'refreshauth') {
            $next($request);
        }
        if ((!$request->session()->has('user_token') || $this->isTokenExpired()) && !$request->session()->has('refresh_token')) {
            $this->doOAuth($request->fullUrl());
            return redirect('getauth');
        }
        if ($this->isTokenExpired() && $request->session()->has('refresh_token')) {

            session()->forget('user_token');
            session(['return' => $request->fullUrl()]);
            return redirect('refreshauth');
        }

        return $next($request);
    }

    public function isTokenExpired()
    {

        if (!session('expires_in') || session('expires_in') === null || session('expires_in') === '') {
            return true;
        }
        $ed = new DateTime(session('expires_in'));
        $nd = new DateTime(now());

        if ($ed > $nd) {
            return false;
        }

        return true;
    }

    public function doOAuth(string $returnURL)
    {

        session()->forget('user_token');
        session(['scope' => $this->scope]);
        session(['return' => $returnURL]);
    }
}
