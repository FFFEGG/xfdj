<?php


namespace App\Tenancy\Extensions;
use App\Cgy;
use App\Group;
use App\Gys;
use App\Merchant;
use App\Product;
use App\ShKc;
use App\User;
use Encore\Admin\Grid\Exporters\ExcelExporter;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;

class TgOrder extends ExcelExporter implements WithMapping,ShouldAutoSize,WithHeadings,WithEvents
{
    use Exportable;

    protected $fileName = '团购结束订单统计.xlsx';

    public function headings(): array
    {
        return [
            '供应商',
            '采购员',
            '供应商电话',
            '产品',
            '数量',
            '规格',
            '状态',
            '拼团结束时间'
        ];
    }

    public function map($row) : array
    {
        return [
            Gys::find($row->u_id)->name,
            Cgy::find(Product::find($row->goods_id)->cgy_id)['name'],
            Gys::find($row->u_id)->tel,
            Product::find($row->goods_id)->title,
            $row->num,
            $row->spec,
            $row->status==0?'未发货':'已发货',
            $row->end_time,
        ];
    }
    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $event->sheet->getColumnDimension('A')->setAutoSize(false)->setWidth(30);
                $event->sheet->getColumnDimension('B')->setAutoSize(false)->setWidth(30);
                $event->sheet->getColumnDimension('C')->setAutoSize(false)->setWidth(30);
                $event->sheet->getColumnDimension('D')->setAutoSize(false)->setWidth(70);
                $event->sheet->getColumnDimension('E')->setAutoSize(false)->setWidth(10);
                $event->sheet->getColumnDimension('F')->setAutoSize(false)->setWidth(30);
                $event->sheet->getColumnDimension('G')->setAutoSize(false)->setWidth(30);
                $event->sheet->getColumnDimension('H')->setAutoSize(false)->setWidth(30);
            }
        ];
    }


    public function getstatus($v)
    {
        if ($v == 1) {
            return '待配送';
        }
        if ($v == 2) {
            return '配送中';
        }
        if ($v == 3) {
            return '配送完成,待提货';
        }
        if ($v == 4) {
            return '已提货';
        }
    }
}
