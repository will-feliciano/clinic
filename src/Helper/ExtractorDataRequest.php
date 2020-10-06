<?php

namespace App\Helper;

use Symfony\Component\HttpFoundation\Request;

class ExtractorDataRequest
{
    private function getAllRequest(Request $request)
    {
        $order = $request->query->get('sort');
        $filter = $request->query->all();
        unset($filter['sort']);

        return [$order, $filter];
    }

    public function getDataOrder(Request $request)
    {
        [$order,] = $this->getAllRequest($request);

        return $order;
    }

    public function getDataFilter(Request $request)
    {
        [, $filter] = $this->getAllRequest($request);

        return $filter;
    }

}