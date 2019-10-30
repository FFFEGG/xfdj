<?php


namespace App\Marketing\Extensions;
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


class ThdZcExporter extends ExcelExporter implements WithMapping,ShouldAutoSize,WithHeadings,WithEvents
{
    use Exportable;

    protected $fileName = '提货点审核.xlsx';

    public function headings(): array
    {
        return [
            '审核编号',
            '微信昵称',
            '姓名',
            '电话',
            '取货点名称',
            '小区名称',
            '地址',
            '业务员',
            '备注',
            '审核状态',
            '申请时间'
        ];
    }

    public function map($row) : array
    {
        return [
            $row->id,
            User::find($row->u_id)['nickname'],
            $row->name,
            $row->tel,
            $row->shopname,
            $row->xqname,
            $row->address,
            XsUser::where('u_id',User::find($row->u_id)['p_id'])->first()['name'],
            $row->msg,
            $this->getstatus($row->is_sh),
            $row->created_at
        ];
    }



    public function registerEvents(): array
    {

        return [
            AfterSheet::class => function(AfterSheet $event) {

            }
        ];
    }

    public function getstatus($status)
    {
        if ($status) {
            return '审核通过';
        } else {
            return '审核中';
        }
    }

}
