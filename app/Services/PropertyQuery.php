<?php

namespace App\Services;

use Illuminate\Http\Request;

class PropertyQuery {

    protected $allowedParams = [
        'price' => ['eq', 'lt', 'gt', 'lte', 'gte'],
        'city' => ['eq'],
        'propertyType' => ['eq'],
        'listingType' => ['eq'],
        'bedrooms'=> ['eq', 'lt', 'gt', 'lte', 'gte'],
        'status' => ['eq'],
    ];

    protected $columnMap = [
        'propertyType' => 'property_type',
        'listingType' => 'listing_type',
    ];

    protected $operatorMap = [
        'eq' => '=',
        'lt' => '<',
        'gt' => '>',
        'lte' => '<=',
        'gte' => '>=',
    ];

    public function transform(Request $request){
        
        $eloQuery = [];

        foreach($this->allowedParams as $param => $operators){
            $query = $request->query($param);

            if(!isset($query)){
                continue;
            }

            $column = $this->columnMap[$param] ?? $param;

            foreach($operators as $operator){
                if(isset($query[$operator])){
                    $eloQuery[] = [
                        $column,
                        $this->operatorMap[$operator],
                        $query[$operator],
                    ];
                }
            }
        }

        return $eloQuery;
    }

}

