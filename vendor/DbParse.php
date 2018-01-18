<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/11/27
 * Time: 20:57
 */

namespace App\Http\vendor;
use Illuminate\Support\Facades\DB;
/**
 * 根据数据库生成各种文件(model controller request view function enum route)
 * Class DbParse
 * @package App\Http\vendor
 */
class DbParse
{
    private $_data = [];

    private $_tableName ;

    //介绍
    private $_message;

    private $_code;

    //数据库前缀
    private $_prefix = 'm_';

    /**
     * 忽略数据库中一下字段
     * @var array
     */
    private $_hidden = ['id', 'add_datetime', 'edit_datetime', 'add_user_id', 'edit_user_id', 'lastupdate', 'modify_time', 'is_delete'];

    /**
     * 数据库注释中添加input标签
     * @var array
     * CREATE TABLE `m_activity` (
        `type` int(10) unsigned NOT NULL DEFAULT '1' COMMENT '类型(0:理财活动、1:贷款活动、)st',
        `home_pic` varchar(255) NOT NULL COMMENT '首页封面图img',
        `start_time` datetime NOT NULL COMMENT '活动开始时间dt',
        `is_timing` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '是否定时(0:非定时、1：定时、)rad',
        `is_top` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '是否置顶(0:未置顶、1：置顶、)ck',
        PRIMARY KEY (`id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
     */
    public $type_arr = [
        'dt' => 'datetime',
        'st' => 'select',
        'img' => 'img',
        'rad' => 'radio',
        'ck' => 'checkbox'
    ];

    /**
     * @return mixed
     */
    public function getCode()
    {
        return $this->_code;
    }

    /**
     * @param mixed $code
     */
    public function setCode($code)
    {
        $this->_code = $code;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getMessage()
    {
        return $this->_message;
    }

    /**
     * @param mixed $message
     */
    public function setMessage($message)
    {
        $this->_message = $message;
        return $this;
    }

    /**
     * @return array
     */
    public function getData()
    {
        return $this->_data;
    }

    /**
     * 获取页面所需要的字段信息
     * @return array
     */
    public function getFormData()
    {
        /**
         * array:8 [▼
        "key" => "键"
        "value" => "值"
        "desc" => "描述"
        ]
         */
        $result = [];
        foreach($this->_data as $key => $item)
        {
            if (!in_array($key, $this->_hidden)) {
                //添加类别
                $result[$key]['type'] = 'text';
                foreach ($this->type_arr as $k => $val) {
                    if (str_contains($item, $k)) {
                        $result[$key]['type'] = $val;
                        //替换标签
                        $item = str_replace($k, '', $item);
                        //替换括号后面的注释
                        $item = preg_replace('/\(.*/', '', $item);
                    }
                }

                $result[$key]['val'] = $item;
            }
        }
        return $result;
    }

    /**
     * 初始化select 所需要的方法
     * @return $this
     */
    public function init_select()
    {
        $className = $this->getClassName($this->_tableName);

        foreach($this->_data as $key => $item)
        {
            if (!in_array($key, $this->_hidden)) {
                if (str_contains($item, 'st')) {
                    //初始化方法
                    //类型(0:理财活动、1:贷款活动)st
                    //获取其中的循环值
                    $res = preg_match_all('/(\d):(\D+)、/', $item, $matches);
                    if (!empty($matches) && !empty($matches[1])) {
                        $matchRes = $matches[1];
                        $str = '';
                        foreach ($matchRes as $k => $value) {
                            $str .= $k . '=>"' . $value . '",
        ';
                        }
                    }
                    $func_str = '
/**
 * '. $item .'
 * @param string $key
 * @return array
 */
function '. lcfirst($className .'_' . $key ). '($key = "")
{
    $arr = [
        '. $str .'
    ];

    if (!empty($key)) {
        return $arr[$key];
    }
    return $arr;
}
';
                    $fileName = app_path('Http/Enum.php');
                    $bakFile = app_path('Http/Enum.php.bak');
                    //复制原来的Enum.php为Enum.php.bak
                    if (!file_exists($bakFile) && copy($fileName, $bakFile)) {
                        $res = $this->filePutContent($fileName, $func_str);
                    }

                }
            }
        }

        return $this;
    }


    /**
     * @param array $data
     */
    public function setData($data)
    {
        $this->_data = $data;
    }

    /**
     * 获取实例化对象
     * @param string $table
     * @param string $message
     * @param string $code
     * @return $this|DbParse
     */
    public static function getInstance($table = '', $message = '', $code = '')
    {
        $self = new self();
        $self->setMessage($message);
        $self->setCode($code);
        if (!empty($table)) {
            return $self->readFromDb($table)->init_select();
        }
        return $self;
    }

    /**
     * 从mysql数据库读取相应的字段信息和注释
     * @param $tableName
     * @return $this
     */
    public function readFromDb($tableName)
    {
        $this->_tableName = $tableName;
        //1.获取数据库中的字段信息
        $data = DB::select('select * from information_schema.columns where table_schema = "'. env("DB_DATABASE") .'"
          and table_name = "'. $tableName .'"');
        //2.分离出其中的字段和注释
        $result = [];
        foreach($data as $row) {
            //过滤\r\n
            $comment = str_replace("\r\n", '', $row->COLUMN_COMMENT);
            $result[$row->COLUMN_NAME] = $comment;
        }

        self::setData($result);
        return $this;
    }


    /**
     * 生成页面表单
     * @return $this
     */
    public function generateForm()
    {
        $data = self::getData();
        $public_path = 'views/admin/public/';
        $template_path = resource_path($public_path . 'template.php');

        ob_start();
        require_once $template_path;
        $template = ob_get_contents();
        ob_end_clean();
        $fileName = resource_path($public_path . 'generate/' . $this->_tableName . '.php');
        $this->filePutContent($fileName, $template);
        return $this;
    }

    /**
     * @param $filename
     * @param $content
     * 生成文件并写入内容
     */
    public function filePutContent($filename,$content)
    {
        if (!is_dir(dirname($filename))) {
            mkdir(dirname($filename));
        }
        if (file_exists($filename)) {
            $bak = $filename . '.bak';
            if (file_exists($bak)) {
                unlink($bak);
            }
            $this->writeContent($bak, $content);
            echo $bak . '文件生成成功' . PHP_EOL;
        } else {
            $this->writeContent($filename, $content);
        }
    }

    /**
     * 写文件
     * @param $filename
     * @param $content
     */
    public function writeContent($filename, $content)
    {
        $file = fopen($filename,'a+');
        fwrite($file,$content);
        fclose($file);
    }

    /**
     * 生成数据库model对象
     * @return $this
     */
    public function generateModel()
    {
        $str = '';
        $modelName = $this->getModelName();
        foreach (self::getData() as $key => $item) {
            $str .= '"' . $key . '",// ' . $item . '
        ';
        }
        $content = '<?php

namespace App\Http\Model;

use Illuminate\Database\Eloquent\Model;
use App\Http\vendor\Myself;

class ' . $modelName . ' extends Model
{
    use Myself;

    protected  $table = "' . $this->_tableName . '";
    //自动更新时间
    public $timestamps = true;

    protected  $fillable = [//字段
        ' . $str . '
    ];

    protected $hidden = ["_token"];


    const  CREATED_AT = "add_datetime";

    const  UPDATED_AT = "edit_datetime";

    //按名称模糊查询
    public function scopeSearch($query, $request)
    {
        if (isset($request["' . $this->getCode() . '"])){
            $query->where("' . $this->getCode() . '","like","%".$request["' . $this->getCode() . '"]."%");
        }

        $is_delete = 0;
        if(isset($request["is_delete"])) {
            $is_delete = $request["is_delete"];
        }
        $query->where("is_delete", "=", intval($is_delete));

        return $query;
    }

}';

        $app_path = app_path('Http/Model/');

        $fileName = $app_path . $modelName . '.php';
        if (!file_exists($fileName)) {
            $this->filePutContent($fileName, $content);
            echo $fileName . '文件生成成功' . PHP_EOL;
        }
        return $this;
    }

    /**
     * 获取最终生成的文件名
     * @return string
     */
    public function getModelName()
    {
        return $this->getClassName($this->_tableName);
    }

    /**
     * 生成Request对象
     * @return $this
     */
    public function generateRequest()
    {

        $modelName = $this->getModelName();
        $modelName .= 'Validate';
        $tem = '    ';
        $message = '';
        foreach (self::getFormData() as $key => $item) {
            $tem .= '"'. $key .'" => "required", //' . $item['val'] . '
            ';
            $message .= '"' . $key . '.required"=>"请填写' . $item['val'] . '",
            ';
        }



        $temp =
'<?php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
class ' . $modelName . ' extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
        ' . $tem . '
        ];
    }

    public function messages()
    {
        return [
            ' . $message . '
        ];
    }
}
';
        $request_path = app_path('Http/Requests/');
        $fileName = $request_path . $modelName . '.php';
        if (!file_exists($fileName)) {
            $this->filePutContent($fileName, $temp);
            echo $fileName . '文件生成成功' . PHP_EOL;
            return $this;
        }
        dd('文件已存在');
    }

    /**
     * 生成route文件
     * @param string $message
     * @return $this
     */
    public function route($message = '')
    {
        $message = $this->getMessage() ?: $message;
        $className = $this->getClassName($this->_tableName);
        $name = lcfirst($className);
        $web_route = app_path('../routes/web_' . $name . '.php');
        $str = 'Route::get("' . $name . '", "' . $className . 'Controller@index")->name("获取'. $message .'列表");//获取'. $message .'列表
Route::post("' . $name . '/update/{id}", "' . $className . 'Controller@update")->name("修改'. $message .'");//修改'. $message .'
Route::get("' . $name . '/edit/{id}", "' . $className . 'Controller@edit")->name("修改'. $message .'显示页面");//修改'. $message .'显示页面
Route::get("' . $name . '/add", "' . $className . 'Controller@add")->name("添加'. $message .'视图");//添加'. $message .'视图
Route::post("' . $name . '/postAdd", "' . $className . 'Controller@postAdd")->name("添加'. $message .'提交");//添加'. $message .'提交
Route::post("' . $name . '/delete_patch", "' . $className . 'Controller@delete_patch")->name("批量删除'. $message .'");//批量删除'. $message .'
Route::post("' . $name . '/delete/{id}", "' . $className . 'Controller@delete")->name("删除'. $message .'");//删除'. $message .'
Route::post("' . $name . '/recover/{id}", "' . $className . 'Controller@recover")->name("恢复'. $message.'");//恢复'. $message .'
Route::post("' . $name . '/up/{id}", "' . $className . 'Controller@up")->name("'. $message.'上架");;//'. $message.'上架
Route::post("' . $name . '/down/{id}", "' . $className . 'Controller@down")->name("'. $message.'下架");//'. $message.'下架';
        if (!file_exists($web_route)) {
            $this->filePutContent($web_route, $str);
            @chmod($web_route, 777);
            echo $web_route . '路由文件生成成功' . PHP_EOL;
            return $this;
        }
        dd('路由文件已存在');
    }

    /**
     * 生成controller文件
     * @return $this
     */
    public function controller()
    {
        $className = $this->getClassName($this->_tableName);
        $name = lcfirst($className);
        $controller_route = app_path('Http/Controllers/Admin/'. $className . 'Controller.php');
            $str = '<?php
namespace App\Http\Controllers\Admin;

use App\Http\Factory\Factory;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Model\\'. $className .';
use App\Http\Requests\\' . $className . 'Validate;
use Illuminate\Support\Facades\Config;
use App\Http\Result\Resp;
use Log;
/**
 * '. $this->getMessage() .'相关信息
 * Class ' . $className . 'Controller
 * @package App\Http\Controllers\Admin
 */
class ' . $className . 'Controller extends Controller
{

    public function index()
    {
        //'. $this->getMessage() .'
        $data = ' . $className . '::search(request()->all())->orderBy("add_datetime", "desc")->paginate(10);
        return view("admin.' . $name . '.index",compact("data"));
    }

    public function edit($id)
    {
        //查询'. $this->getMessage() .'信息
        $info = ' . $className . '::where("id", $id)->first();
        return view("admin.' . $name . '.edit", compact("info"));
    }

    /**
     * 更新'. $this->getMessage() .'信息
     * @param Request $request
     */
    public function update(' . $className . 'Validate $request, $id)
    {
        $info = '. $className .'::find($id);
        $info->fill($request->all())->save();
        if (!$info) return back()->withInput($request->all())->with("error", "更新'. $this->getMessage() .'失败");
        return redirect("admin/' . $name . '");
    }

    /**
     * 添加'. $this->getMessage() .'视图
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function add()
    {
        return view("admin.' . $name . '.add");
    }
    
    /**
     * @param ' . $className . 'Validate $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function postAdd(' . $className . 'Validate $request)
    {
        $info = ' . $className . '::create($request->all());
        if (!$info) return back()->withInput($request->all())->with("error", "新增'. $this->getMessage() .'失败");
        return redirect("admin/' . $name . '");
    }
    
    
    /**
     * 删除
     */
    public function delete($id)
    {
        $info = ' . $className . '::where("id", $id)->first();
        //判断
        $resp = $info->fill(["is_delete" => 1])->save();
        if ($resp) {
            Resp::Ajax(SUCCESS_CODE, "删除成功");
        } else {
            Resp::Ajax(-1, Resp::ERROR_COMMENTDELETE);
        }
    }

    /**
     * 恢复
     */
    public function recover($id)
    {
        $info = ' . $className . '::where("id", $id)->first();
        //判断
        $resp = $info->fill(["is_delete" => 0])->save();
        if ($resp) {
            Resp::Ajax(SUCCESS_CODE, "恢复成功");
        } else {
            Resp::Ajax(-1, Resp::ERROR_COMMENTRECOVER);
        }
    }

    /**
     * 批量删除
     */
    public function delete_patch(Request $request)
    {
        $ids = $request->input("ids");
        $id_arr = array_filter(explode(",", $ids));
        if (empty($id_arr)) {
            Resp::Ajax(-2, "没有要删除的id");
        }
        $resp = ' . $className . '::whereIn("id", $id_arr)->update(["is_delete" => 1]);
        if ($resp) {
            Resp::Ajax(SUCCESS_CODE, "删除成功");
        } else {
            Resp::Ajax(-1, Resp::ERROR_COMMENTDELETE);
        }
    }
    
    
    /**
     * 上架
     */
    public function up($id)
    {
        $info = ' . $className . '::where("id", $id)->first();
        //判断
        $resp = $info->fill(["status" => 1])->save();
        if ($resp) {
            Resp::Ajax(SUCCESS_CODE, "上架成功");
        } else {
            Resp::Ajax(-1, "上架失败");
        }
    }

    /**
     * 下架
     */
    public function down($id)
    {
        $info = ' . $className . '::where("id", $id)->first();
        //判断
        $resp = $info->fill(["status" => 0])->save();
        if ($resp) {
            Resp::Ajax(SUCCESS_CODE, "下架成功");
        } else {
            Resp::Ajax(-1, "下架失败");
        }
    }

}
';
        $this->filePutContent($controller_route, $str);
        return $this;
    }


    /**
     * 生成添加页面view
     * @return $this
     */
    public function addview()
    {
        $className = $this->getClassName($this->_tableName);
        $name = lcfirst($className);
        $data = $this->getFormData();
        $template_path = 'views/admin/template/';
        $template_add = resource_path($template_path . 'add.blade.php');

        //生成title（title）
        $title = '添加' . $this->_message;
        //生成页面提交地址（addAction）
        $lowerName = strtolower($className);
        $addAction = '{{url("admin/' . $name . '/postAdd")}}';
        $listAction = '{{url("admin/' . $name . '")}}';
        $template = View::includeAddFile($template_add, $data, $title, $addAction, $lowerName, $listAction);
        //add 页面生成地址
        $fileName = resource_path($template_path . "../{$name}/add.blade.php");
        $this->filePutContent($fileName, $template);
        return $this;
    }

    /**
     * 生成列表页面
     * @param $code 搜索的字段
     */
    public function indexView($code)
    {
        $className = $this->getClassName($this->_tableName);
        $name = lcfirst($className);
        $template_path = 'views/admin/template/';
        $template_index = resource_path($template_path . 'index.blade.php');
        //生成title（title）
        $title = $this->_message;
        $data = $this->getFormData();
        $message = $data[$code]['val'] ?? '';
        $addUrl = '{{url("admin/'. $name .'/add")}}';
        $addAuthUrl = 'admin/'. $name .'/add';
        $editUrl = '{{ url("admin/'.$name.'/edit/" . $item->id) }}';
        $editAuthUrl = 'admin/'.$name.'/edit/{id}';
        $template = View::includeIndexFile($template_index, $data, $title, $code, $message, $addUrl, $name, $editUrl, $editAuthUrl, $addAuthUrl);

        //add 页面生成地址
        $fileName = resource_path($template_path . "../{$name}/index.blade.php");
        $this->filePutContent($fileName, $template);
        return $this;
    }


    /**
     * 生成编辑页面
     * @return $this
     */
    public function editView()
    {
        $className = $this->getClassName($this->_tableName);
        $name = lcfirst($className);
        $data = $this->getFormData();
        $template_path = 'views/admin/template/';
        $template_edit = resource_path($template_path . 'edit.blade.php');

        //生成title（title）
        $title = '编辑' . $this->_message;
        //生成页面提交地址（addAction）
        $updateAction = '{{url("admin/' . $name . '/update/" . $info->id)}}';
        $listAction = '{{url("admin/' . $name . '")}}';
        $lowerName = strtolower($className);
        $template = View::includeEditFile($template_edit, $data, $title, $updateAction, $lowerName, $listAction);

        //add 页面生成地址
        $fileName = resource_path($template_path . "../{$name}/edit.blade.php");
        $this->filePutContent($fileName, $template);
        return $this;
    }

    /**
     * 根据数据库名称获取类名称
     * @param $tableName
     * @return string
     */
    public function getClassName($tableName)
    {
        //m_为数据库前缀
        preg_match("/^$this->_prefix(.*)/", $tableName, $match);
        if (!empty($match) && !empty($match[1])) {
            return $this->convertUnderline($match[1]);
        }
        return $tableName;
    }

    /**
     * 将下划线命名转换为驼峰式命名
     * @param $str
     * @param bool $ucfirst
     * @return string
     */
    public function convertUnderline( $str , $ucfirst = true)
    {
        $str = ucfirst($str);
        while(($pos = strpos($str , '_'))!==false)
            $str = substr($str , 0 , $pos).ucfirst(substr($str , $pos+1));

        return $ucfirst ? ucfirst($str) : $str;
    }


}