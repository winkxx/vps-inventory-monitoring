<?php
namespace app\index\command;

use think\console\Command;
use think\console\Input;
use think\console\Output;
use think\console\input\Argument;
use think\console\input\Option;
use app\index\model\User;
use app\index\model\Index;
use app\index\model\Log;
use think\Config;

class VpsTest extends Command{
    protected $debug = true ;
    protected function configure(){
        $this->setName("VpsTest")->setDescription("This is Vps Test Command");
    }
    protected function execute(Input $input, Output $output){
        //获取参数值
        Config::load(APP_PATH . 'index/config.php'); 
        $log = new Log;
        $index =  new Index;
        $user = new User;
        while (1) {
            $output->writeln(date("Y-m-d h:i:s"));
            //$list = $index->where(["status"=>1])->select();
            $list =  $index->alias('a')->join('xm_company c',"c.id = a.companyid" )->field("a.*,c.name as companyname,c.url as companyurl")->where(["status"=>1])->select();
            $r = [];
            $host = config("app.host");
            foreach ($list as $value){
                $curl = go_curl($value["vurl"],"get");
                if (config("app.testdebug")){
                    file_put_contents(TEMP_PATH . "/debug.txt","------[START]------\n".date("Y-m-d h:i:s")."\n{$curl['RequestHeader']}\n\n{$curl['ResponseHeader']}\n{$curl['Body']}\n------[END]------",FILE_APPEND);
                }
                $str = $curl["Body"];
                $a = eval($value["vf"]);
                $r[] = "{$value['name']} --- " . (($a) ? 'true' : 'false');
                if ($a != $value["stock"]){
                    Index::where("id",$value["id"])->update(["stock"=>$a]); //save(["stock"=>$a],["id"=>$value["id"]]);
                    Log::where("indexid",$value["id"])->update(["status"=>$a]);
                    //$log->data(["status"=>$a,"indexid"=>$value["id"]])->isUpdate(false)->save();
                }
                if ($value["stock"] == false && $a == true){
                    $p = $user->where("find_in_set({$value['id']},subscribe)")->select();
                    $title = "补货辣";
                    $content = "{$value['companyname']} 的 {$value['name']} 补货了。\n{$value['cpu']}核心\n {$value['ram']}内存\n {$value['disk']}硬盘\n{$value['flow']}流量\n {$value['bandwidth']}带宽\n 月付 {$value['monthly']}\n[测评地址]($host/ceping/{$value['id']})\n[购买地址]($host/buy/{$value['id']})\n备注：{$value['remark']}";
                    foreach ($p as $k => $v) {
                        if ($v["ftsckey"] != ""){
                            go_curl("https://sc.ftqq.com/{$v['ftsckey']}.send","post", ["text"=>$title,"desp"=>$content]);
                        }//server 酱
                        if ($v["tgsckey"] != ""){
                            go_curl("https://vps.honoka.club/tgbot.php","post", ["method"=>"send","content"=>$content,"sckey"=>$v["tgsckey"]]);
                        }
                    }
                    if (config("app.tgchannelsckey") != ""){
                        go_curl("https://vps.honoka.club/tgbot.php","post", ["method"=>"send","content"=>$content,"sckey"=>config("app.tgchannelsckey")]);
                    }
                    
                }
                sleep(5);
            }
            $output->writeln( join($r,"\n") . "\nOK" );

            $output->writeln("Success !" );
            $output->writeln(date("Y-m-d h:i:s"));
            sleep(config("app.testtick"));
        }
        return ;
    }
}
