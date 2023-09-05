<?php

namespace App\Services\Common;

use Illuminate\Pagination\LengthAwarePaginator;

class CustomizePaginate extends LengthAwarePaginator
{
    /**
     * 自定义分页函数
     * @author Neo
     * @return mixed
     */
    public function toArray()
    {
        return [
            'current_page' => $this->currentPage(),
            'data' => $this->items->toArray(),
            'per_page' => (int) $this->perPage(),
            'total' => $this->total(),
        ];
    }
}
