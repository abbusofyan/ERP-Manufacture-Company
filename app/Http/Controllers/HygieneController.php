<?php

namespace App\Http\Controllers;

use App\Models\RefrigerationSale;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Redirector;
use Inertia\Inertia;
use Inertia\Response;

class HygieneController extends RefrigerationSaleController
{
    /**
     * Display a listing of the resource.
     *
     * @param $shipment
     * @param Request $request
     * @param int $post_type
     * @return Response
     */
    public function index($shipment, Request $request, int $post_type = RefrigerationSale::POST_TYPE_HYGIENE): Response
    {
        return parent::index($shipment, $request, $post_type);
    }

    /**
     * Display a listing of the resource.
     *
     * @param $shipment
     * @param int $post_type
     * @return Response
     */
    public function create($shipment, int $post_type = RefrigerationSale::POST_TYPE_HYGIENE): Response
    {
        return parent::create($shipment, $post_type);
    }

    /**
     * Display a listing of the resource.
     *
     * @param $shipment
     * @param Request $request
     * @param int $post_type
     * @return Application|Redirector|RedirectResponse
     */
    public function store($shipment, Request $request, int $post_type = RefrigerationSale::POST_TYPE_HYGIENE): Application|RedirectResponse|Redirector
    {
        return parent::store($shipment, $request, $post_type);
    }


    /**
     * Show the form for creating a new resource.
     *
     * @param $shipment
     * @param RefrigerationSale $hygiene
     * @param int $post_type
     * @return Response
     */
    public function edit($shipment, RefrigerationSale $hygiene, int $post_type = RefrigerationSale::POST_TYPE_HYGIENE): Response
    {
        return parent::edit($shipment, $hygiene, $post_type);
    }


    /**
     * Update a resource.
     *
     * @param $shipment
     * @param Request $request
     * @param RefrigerationSale $hygiene
     * @param int $post_type
     * @return Redirector|RedirectResponse|Application
     */
    public function update($shipment, Request $request, RefrigerationSale $hygiene, int $post_type = RefrigerationSale::POST_TYPE_HYGIENE): Redirector|RedirectResponse|Application
    {
        return parent::update($shipment, $request, $hygiene, $post_type);
    }


    /**
     * Display the specified resource.
     *
     * @param $shipment
     * @param RefrigerationSale $hygiene
     * @param int $post_type
     * @return Response
     */
    public function show($shipment, RefrigerationSale $hygiene, int $post_type = RefrigerationSale::POST_TYPE_HYGIENE): Response
    {
        return parent::show($shipment, $hygiene, $post_type);
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param $shipment
     * @param RefrigerationSale $hygiene
     * @param int $post_type
     * @return RedirectResponse
     */
    public function destroy($shipment, RefrigerationSale $hygiene, int $post_type = RefrigerationSale::POST_TYPE_HYGIENE): RedirectResponse
    {
        return parent::destroy($shipment, $hygiene, $post_type);
    }
}
