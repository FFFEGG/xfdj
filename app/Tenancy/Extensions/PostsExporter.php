<?php


namespace App\Tenancy\Extensions;
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

class PostsExporter extends ExcelExporter implements WithMapping,ShouldAutoSize,WithHeadings,WithEvents
{
    use Exportable;

    protected $fileName = '物流出货表.xlsx';

    public function headings(): array
    {
        return [
            '序号',
            '取货点',
            '收货人名字',
            '收货人电话',
            '产品名称',
            '订单价格',
            '订单数量',
            '到货时间',
            '配送时间',
            '配送员',
            '订单状态',
            '支付时间',
            '留言',
            '分享人',
            '销售员',
        ];
    }

    public function map($row) : array
    {
        return [
            $row->id,
            Group::find($row->group_id)->title,
            $row->name,
            $row->tel,
            data_get($row, 'ordergoods.goods.title'),
            $row->price,
            data_get($row, 'ordergoods.num'),
            $row->hd_time,
            $row->ps_time,
            data_get($row, 'psorder.loguser.name'),
            $this->getstatus($row->status),
            $row->paid_at,
            $row->msg,
            User::find($row->leader_id)['nickname'],
            XsUser::whereUId(User::find($row->leader_id)['p_id'])->first()['name']
        ];
    }
    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $event->sheet->getColumnDimension('A')->setAutoSize(false)->setWidth(10);
                $event->sheet->getColumnDimension('B')->setAutoSize(false)->setWidth(30);
                $event->sheet->getColumnDimension('E')->setAutoSize(false)->setWidth(50);
                $event->sheet->getColumnDimension('F')->setAutoSize(false)->setWidth(10);
                $event->sheet->getColumnDimension('G')->setAutoSize(false)->setWidth(10);
                $event->sheet->getColumnDimension('J')->setAutoSize(false)->setWidth(10);
                $event->sheet->getColumnDimension('K')->setAutoSize(false)->setWidth(10);
                $event->sheet->getColumnDimension('M')->setAutoSize(false)->setWidth(30);
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
