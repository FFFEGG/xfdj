<?php


namespace App\Admin\Extensions;
use App\Group;
use App\User;
use App\XsUser;
use Encore\Admin\Grid\Exporters\ExcelExporter;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;

class SpCwxxLrExporter extends ExcelExporter implements WithMapping,ShouldAutoSize,WithHeadings,WithEvents
{
    use Exportable;

    protected $fileName = '商品财务信息.xlsx';

    public function headings(): array
    {
        return [
            '商品编号',
            '商品名称',
            '售价',
            '成本价',
            '进货税率（%）',
            '销售税率（%）',
            '锁定状态'
        ];
    }

    public function map($row) : array
    {
        return [
            $row->id,
            $row->title,
            $row->price,
            $row->cbj,
            $row->jhsl,
            $row->xssl,
            $row->sd?'已锁定':'未锁定',

        ];
    }
    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $event->sheet->getColumnDimension('A')->setAutoSize(false)->setWidth(10);
                $event->sheet->getColumnDimension('B')->setAutoSize(false)->setWidth(70);
                $event->sheet->getColumnDimension('C')->setAutoSize(false)->setWidth(30);
                $event->sheet->getColumnDimension('D')->setAutoSize(false)->setWidth(30);
                $event->sheet->getColumnDimension('E')->setAutoSize(false)->setWidth(30);
                $event->sheet->getColumnDimension('F')->setAutoSize(false)->setWidth(30);
                $event->sheet->getColumnDimension('G')->setAutoSize(false)->setWidth(30);
            }
        ];
    }

}
