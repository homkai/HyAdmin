<?php
namespace Common\Behavior;
use Think\Behavior;
/**
 * 敏感词过滤
 * @author Homkai
 *
 */
 class HyFilterWordsBehavior extends Behavior{
 	
    public function run(&$content){
        if(!C('FILTER_WORDS.on')) return;
        $keywordstring=C('FILTER_WORDS.words');
        $keywordarray=explode(',',$keywordstring);
        $replaceword=C('FILTER_WORDS.replace');
        $replace=array();
        if(is_array($keywordarray)){
            foreach($keywordarray as $key=>$value){
                $replace[$value]=$replaceword;
            }
        }
        $content = str_replace(array_keys($replace),array_values($replace),$content);
    }
 }