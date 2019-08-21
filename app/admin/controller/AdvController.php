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

use app\admin\model\AdvModel;
use cmf\controller\AdminBaseController;
use think\Db;

class AdvController extends AdminBaseController
{

    /**
     * 幻灯片列表
     * @adminMenu(
     *     'name'   => '幻灯片管理',
     *     'parent' => 'admin/Setting/default',
     *     'display'=> true,
     *     'hasView'=> true,
     *     'order'  => 40,
     *     'icon'   => '',
     *     'remark' => '幻灯片管理',
     *     'param'  => ''
     * )
     */
    public function index()
    {
        $advPostModel = new AdvModel();
        $advs         = $advPostModel->where(['delete_time' => ['eq', 0]])->select();
        $this->assign('advs', $advs);
        return $this->fetch();
    }

    /**
     * 添加幻灯片
     * @adminMenu(
     *     'name'   => '添加幻灯片',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> true,
     *     'order'  => 10000,
     *     'icon'   => '',
     *     'remark' => '添加幻灯片',
     *     'param'  => ''
     * )
     */
    public function add()
    {
        return $this->fetch();
    }

    /**
     * 添加幻灯片提交
     * @adminMenu(
     *     'name'   => '添加幻灯片提交',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> false,
     *     'order'  => 10000,
     *     'icon'   => '',
     *     'remark' => '添加幻灯片提交',
     *     'param'  => ''
     * )
     */
    public function addPost()
    {
        $data           = $this->request->param();
        $advPostModel = new AdvModel();
        $result         = $advPostModel->validate(true)->save($data);
        if ($result === false) {
            $this->error($advPostModel->getError());
        }
        $this->success("添加成功！", url("adv/index"));
    }

    /**
     * 编辑幻灯片
     * @adminMenu(
     *     'name'   => '编辑幻灯片',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> true,
     *     'order'  => 10000,
     *     'icon'   => '',
     *     'remark' => '编辑幻灯片',
     *     'param'  => ''
     * )
     */
    public function edit()
    {
        $id             = $this->request->param('id');
        $advPostModel = new AdvModel();
        $result         = $advPostModel->where('id', $id)->find();
        $this->assign('result', $result);
        return $this->fetch();
    }

    /**
     * 编辑幻灯片提交
     * @adminMenu(
     *     'name'   => '编辑幻灯片提交',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> false,
     *     'order'  => 10000,
     *     'icon'   => '',
     *     'remark' => '编辑幻灯片提交',
     *     'param'  => ''
     * )
     */
    public function editPost()
    {
        $data           = $this->request->param();
        $advPostModel = new AdvModel();
        $result         = $advPostModel->validate(true)->save($data, ['id' => $data['id']]);
        if ($result === false) {
            $this->error($advPostModel->getError());
        }
        $this->success("保存成功！", url("adv/index"));
    }

    /**
     * 删除幻灯片
     * @adminMenu(
     *     'name'   => '删除幻灯片',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> false,
     *     'order'  => 10000,
     *     'icon'   => '',
     *     'remark' => '删除幻灯片',
     *     'param'  => ''
     * )
     */
    public function delete()
    {
        $id             = $this->request->param('id', 0, 'intval');
        $advPostModel = new AdvModel();
        $result       = $advPostModel->where(['id' => $id])->find();
        if (empty($result)){
            $this->error('幻灯片不存在!');
        }

        //如果存在页面。则不能删除。
        $advPostCount = Db::name('adv_item')->where('adv_id', $id)->count();
        if ($advPostCount > 0) {
            $this->error('此幻灯片有页面无法删除!');
        }

        $data         = [
            'object_id'   => $id,
            'create_time' => time(),
            'table_name'  => 'adv',
            'name'        => $result['name']
        ];

        $resultadv = $advPostModel->save(['delete_time' => time()], ['id' => $id]);
        if ($resultadv) {
            Db::name('recycleBin')->insert($data);
        }
        $this->success("删除成功！", url("adv/index"));
    }
}