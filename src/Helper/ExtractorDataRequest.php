<?php

namespace App\Helper;

use Symfony\Component\HttpFoundation\Request;

class ExtractorDataRequest
{
    private function getAllRequest(Request $request)
    {
        $queryString = $request->query->all();

        $order = array_key_exists('sort', $queryString)? $queryString['sort']: null;
        unset($queryString['sort']);
        $page = array_key_exists('page', $queryString)? $queryString['page']: 1;
        unset($queryString['page']);
        $itens = array_key_exists('itens', $queryString)? $queryString['itens']: 15;
        unset($queryString['itens']);

        return [$order, $queryString, $page, $itens];
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

    public function getDataPages(Request $request)
    {
        [, , $page, $itens] = $this->getAllRequest($request);

        return [$page, $itens];
    }
}