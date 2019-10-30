<?php


namespace App\Marketing\Extensions;
use App\Group;
use Encore\Admin\Grid\Exporters\ExcelExporter;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
class PostsExporter extends ExcelExporter implements WithMapping,ShouldAutoSize,WithHeadings,WithEvents
{
    use Exportable;

    protected $fileName = '提货点.xlsx';

    public function headings(): array
    {
        return [
            'title',
            'address',
            'longitude',
            'latitude',
            'coord_type',
            'tags',
            'polygons',
            '',
            'id_dy',
            'icon_style_id',
            'tel',
            'name',
            'group_id',
            'xqname',
        ];
    }

    public function map($row) : array
    {
        return [
            $row->title,
            $row->address,
            $row->longitude,
            $row->latitude,
            3,
            '',
            '',
            '',
            0,
            '',
            $row->tel,
            $row->name,
            $row->id,
            $row->xqname
        ];
    }
}
