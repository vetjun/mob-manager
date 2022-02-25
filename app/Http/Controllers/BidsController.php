<?php

namespace App\Http\Controllers;

use App\Models\Bids;
use Illuminate\Http\Request;

class BidsController
{

    public function add(Request $request)
    {
        $all = $request->all();
        $newBid = new Bids();
        if (isset($all['client_name'])){
            $newBid->setAttribute('client_name', $all['client_name']);
        }
        if (isset($all['bid_date'])){
            $newBid->setAttribute('bid_date', $all['bid_date']);
        }
        if (isset($all['bid_code'])){
            $newBid->setAttribute('bid_code', $all['bid_code']);
        }
        if (isset($all['bid_amount'])){
            $newBid->setAttribute('bid_amount', $all['bid_amount']);
        }
        if (isset($all['status'])){
            $newBid->setAttribute('status', $all['status']);
        }
        $newBid->save();
        return response()->json([
            'data' => $newBid
        ]);

    }
    public function get(){
        $bids = Bids::all();
        return response()->json([
            'data' => $bids
        ]);
    }

    public function edit(Request $request){
        $all = $request->all();
        $bidId = $all['id'];
        /** @var Bids $bid */
        $bid = Bids::where('id', $bidId)->first();
        if (isset($all['client_name']))
        {
            $bid['client_name'] = $all['client_name'];
        }
        if (isset($all['bid_code']))
        {
            $bid['bid_code'] = $all['bid_code'];
        }
        if (isset($all['bid_date']))
        {
            $bid['bid_date'] = $all['bid_date'];
        }
        if (isset($all['bid_amount']))
        {
            $bid['bid_amount'] = $all['bid_amount'];
        }
        if (isset($all['status']))
        {
            $bid['status'] = $all['status'];
        }
        $bid->save();
        return response()->json([
            'data' => $bid
        ]);
    }

    public function delete(Request $request){
        $all = $request->all();
        $bidId = $all['id'];
        Bids::destroy($bidId);
        return response()->json([
            'id' => $bidId
        ]);
    }
}
