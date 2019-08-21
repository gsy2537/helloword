<?php
// +----------------------------------------------------------------------
// | ThinkCMF [ WE CAN DO IT MORE SIMPLE ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013-2018 http://www.thinkcmf.com All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 小夏 < 449134904@qq.com>
// +----------------------------------------------------------------------
namespace app\admin\controller;

use think\Db;
use cmf\controller\AdminBaseController;
use app\admin\model\AdvItemModel;

class AdvItemController extends AdminBaseController
{
    /**
     * 广告页面列表
     * @adminMenu(
     *     'name'   => '广告页面列表',
     *     'parent' => 'admin/Adv/index',
     *     'display'=> false,
     *     'hasView'=> true,
     *     'order'  => 10000,
     *     'icon'   => '',
     *     'remark' => '广告页面列表',
     *     'param'  => ''
     * )
     */
    public function index()
    {
        $id      = $this->request->param('adv_id');
        $advId = !empty($id) ? $id : 1;
        $result  = Db::name('advItem')->where(['adv_id' => $advId])->select()->toArray();

        $this->assign('adv_id', $id);
        $this->assign('result', $result);
        return $this->fetch();
    }

    /**
     * 广告页面添加
     * @adminMenu(
     *     'name'   => '广告页面添加',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> true,
     *     'order'  => 10000,
     *     'icon'   => '',
     *     'remark' => '广告页面添加',
     *     'param'  => ''
     * )
     */
    public function add()
    {
        $advId = $this->request->param('adv_id');
        $this->assign('adv_id', $advId);
        return $this->fetch();
    }

    /**
     * 广告页面添加提交
     * @adminMenu(
     *     'name'   => '广告页面添加提交',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> false,
     *     'order'  => 10000,
     *     'icon'   => '',
     *     'remark' => '广告页面添加提交',
     *     'param'  => ''
     * )
     */
    public function addPost()
    {
        $data = $this->request->param();
        Db::name('advItem')->insert($data['post']);
        $this->success("添加成功！", url("advItem/index", ['adv_id' => $data['post']['adv_id']]));
    }

    /**
     * 广告页面编辑
     * @adminMenu(
     *     'name'   => '广告页面编辑',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> true,
     *     'order'  => 10000,
     *     'icon'   => '',
     *     'remark' => '广告页面编辑',
     *     'param'  => ''
     * )
     */
    public function edit()
    {
        $id     = $this->request->param('id');
        $result = Db::name('advItem')->where(['id' => $id])->find();

        $this->assign('result', $result);
        $this->assign('adv_id', $result['adv_id']);
        return $this->fetch();
    }

    /**
     * 广告页面编辑
     * @adminMenu(
     *     'name'   => '广告页面编辑提交',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> false,
     *     'order'  => 10000,
     *     'icon'   => '',
     *     'remark' => '广告页面编辑提交',
     *     'param'  => ''
     * )
     */
    public function editPost()
    {
        $data = $this->request->param();

        $data['post']['image'] = cmf_asset_relative_url($data['post']['image']);

        Db::name('advItem')->update($data['post']);

        $this->success("保存成功！", url("AdvItem/index", ['adv_id' => $data['post']['adv_id']]));

    }

    /**
     * 广告页面删除
     * @adminMenu(
     *     'name'   => '广告页面删除',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> false,
     *     'order'  => 10000,
     *     'icon'   => '',
     *     'remark' => '广告页面删除',
     *     'param'  => ''
     * )
     */
    public function delete()
    {
        $id     = $this->request->param('id', 0, 'intval');

        $advItem = Db::name('advItem')->find($id);

        $result = Db::name('advItem')->delete($id);
        if ($result) {
            //删除图片。
//            if (file_exists("./upload/".$advItem['image'])){
//                @unlink("./upload/".$advItem['image']);
//            }
            $this->success("删除成功！", url("AdvItem/index",["adv_id"=>$advItem['adv_id']]));
        } else {
            $this->error('删除失败！');
        }

    }

    /**
     * 广告页面隐藏
     * @adminMenu(
     *     'name'   => '广告页面隐藏',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> false,
     *     'order'  => 10000,
     *     'icon'   => '',
     *     'remark' => '广告页面隐藏',
     *     'param'  => ''
     * )
     */
    public function ban()
    {
        $id = $this->request->param('id', 0, 'intval');
        if ($id) {
            $rst = Db::name('advItem')->where(['id' => $id])->update(['status' => 0]);
            if ($rst) {
                $this->success("广告隐藏成功！");
            } else {
                $this->error('广告隐藏失败！');
            }
        } else {
            $this->error('数据传入失败！');
        }
    }

    /**
     * 广告页面显示
     * @adminMenu(
     *     'name'   => '广告页面显示',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> false,
     *     'order'  => 10000,
     *     'icon'   => '',
     *     'remark' => '广告页面显示',
     *     'param'  => ''
     * )
     */
    public function cancelBan()
    {
        $id = $this->request->param('id', 0, 'intval');
        if ($id) {
            $result = Db::name('advItem')->where(['id' => $id])->update(['status' => 1]);
            if ($result) {
                $this->success("广告启用成功！");
            } else {
                $this->error('广告启用失败！');
            }
        } else {
            $this->error('数据传入失败！');
        }
    }

    /**
     * 广告页面排序
     * @adminMenu(
     *     'name'   => '广告页面排序',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> false,
     *     'order'  => 10000,
     *     'icon'   => '',
     *     'remark' => '广告页面排序',
     *     'param'  => ''
     * )
     */
    public function listOrder()
    {
        $advItemModel = new  AdvItemModel();
        parent::listOrders($advItemModel);
        $this->success("排序更新成功！");
    }
}