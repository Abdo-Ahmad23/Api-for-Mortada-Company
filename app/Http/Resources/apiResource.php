<?php

namespace App\Http\Resources;

use App\Http\controllers\ApiResonseTrait;
use App\Models\User;
use Illuminate\Http\Resources\Json\JsonResource;

class apiResource extends JsonResource
{
    use ApiResonseTrait;
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'id'=>$this->id,
            'name'=>$this->name,
            'email'=>$this->email,
            'password'=>$this->password
        ];
    }

}