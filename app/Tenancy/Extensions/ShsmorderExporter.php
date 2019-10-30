<?php


namespace App\Tenancy\Extensions;
use App\Group;
use App\Merchant;
use App\ShKc;
use App\User;
use Encore\Admin\Grid\Exporters\ExcelExporter;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;

class ShsmorderExporter extends ExcelExporter implements WithMapping,ShouldAutoSize,WithHeadings,WithEvents
{
    use Exportable;

    protected $fileName = '商户扫码订单.xlsx';

    public function headings(): array
    {
        return [
            '序号',
            '用户',
            '商品',
            '商户',
            '数量',
            '金额',
            '时间'
        ];
    }

    public function map($row) : array
    {
        return [
            $row->id,
            User::find($row->u_id)->nickname,
            ShKc::find($row->sh_goods_id)->goods->title,
            Merchant::whereUId(ShKc::find($row->sh_goods_id)->u_id)->first()->name,
            $row->num,
            $row->price,
            $row->created_at,
        ];
    }
    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $event->sheet->getColumnDimension('A')->setAutoSize(false)->setWidth(10);
                $event->sheet->getColumnDimension('B')->setAutoSize(false)->setWidth(30);
                $event->sheet->getColumnDimension('E')->setAutoSize(false)->setWidth(50);
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
