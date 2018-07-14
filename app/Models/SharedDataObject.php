<?php namespace App\Models;

use Illuminate\Support\Facades\Validator;

class SharedDataObject
{

    /*
     * The internal items class for this is structured like this:
     *
     * $items = [
     *      0 => [
     *          "count"     => 100,
     *          "title"     => "Test title goes here",
     *          "image"     => "http://imagepath.com/image.png"
     *          "position"  => 1
     *      ]
     * ]
     */

    private $items = [];

    public function addItem($array)
    {

        $validator = Validator::make($array, [
            'count'     => 'required',
            'title'     => 'required',
            'position'  => 'required'
        ]);

        if ($validator->fails()) {
            throw new \Exception("Passed item does not have required fields present.");
        }

        $this->items[] = $array;
    }

    public function getItems()
    {
        return $this->items;
    }
}
