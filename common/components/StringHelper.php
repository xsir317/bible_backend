<?php
/** * Created by PhpStorm.
 * User: hujie
 * Date: 2015/4/16
 * Time: 17:07
 */

namespace common\components;

class StringHelper {

    private static $allow_str = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';

    /**
     * @param $code string 压缩后的code
     * @param $chars string 字典
     * @return int
     */
    public static function code2num($code,$chars = '')
    {
        $dict = $chars ? : self::$allow_str;
        $code = trim($code);
        $base = strlen($dict);
        $ret = 0;//返回值
        $len = strlen($code);
        for($i=0;$i<$len;$i++)//遍历，累加，该位的权值乘以数值
        {
            $_pos = strpos($dict,$code[$i]);
            if($_pos === false)
            {
                return false;
            }
            $ret += pow($base,$len-$i-1)*$_pos;
        }
        return $ret;
    }

    /**
     * @param $num int 使用一个字符串来压缩数字，转化为一个不可读的N进制数字。
     * @param $chars string
     * @return string 返回转换后的N进制字符串
     */
    public static function num2code($num,$chars = '')
    {
        $dict = $chars ? : self::$allow_str;
        $num = abs($num);
        $base = strlen($dict);
        $ret = '';
        while($num)
        {
            $lowest = $num%$base;//求余数，就是最低位
            $ret = $dict[$lowest].$ret;
            $num -= $lowest;
            $num /= $base;
        }
        if(!$ret)
            $ret = $dict[0];
        return $ret;
    }

    public static function mask($string,$from=3,$len=0,$mask_char='*')
    {
        $mask_str = mb_substr($string,0,$from);
        $strlen = mb_strlen($string,'UTF-8');
        if($from + $len >= $strlen)
        {
            $mask_str .= str_repeat($mask_char,($strlen-$from)>=0 ? ($strlen-$from) : 0);
        }
        else
        {
            $mask_str .= str_repeat($mask_char,$len).mb_substr($string,($from+$len));
        }
        return $mask_str;
    }

    public static function randstr($len = 1 ,$chars="")
    {
        if($chars==""){
            $chars = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';      
        }        
        $charlen = strlen($chars);
        $return = '';
        while(strlen($return) < $len)
        {
            $return .= $chars[rand(0,$charlen-1)];
        }
        return $return;
    }

    public static function checkIdCard($idcard)
    {
        // 15位直接跳过  
        if (preg_match('/^[1-9]\d{7}((0\d)|(1[0-2]))(([0|1|2]\d)|3[0-1])\d{3}$/', $idcard)) {
            return true;
        }

        if ( ! preg_match('/^[1-9]\d{5}[1-9]\d{3}((0\d)|(1[0-2]))(([0|1|2]\d)|3[0-1])\d{3}(\d|[xX])$/', $idcard)) {
            return false;
        }

        // 取出本体码
        $idcard_base = substr($idcard, 0, 17);
 
        // 取出校验码
        $verify_code = substr($idcard, 17, 1);
 
        // 加权因子
        $factor = array(7, 9, 10, 5, 8, 4, 2, 1, 6, 3, 7, 9, 10, 5, 8, 4, 2);
 
        // 校验码对应值
        $verify_code_list = array('1', '0', 'X', '9', '8', '7', '6', '5', '4', '3', '2');
 
        // 根据前17位计算校验码
        $total = 0;
        for($i=0; $i<17; $i++) {
            $total += substr($idcard_base, $i, 1) * $factor[$i];
        }

        // 取模
        $mod = $total % 11;
 
        // 比较校验码
        return $verify_code == $verify_code_list[$mod];
    }

    public static function xml_encode($arr)
    {
        $xml = "<xml>";
        foreach ($arr as $key => $val)
        {
            if (is_numeric($val))
                $xml.="<".$key.">".$val."</".$key.">";
            else
                $xml.="<".$key."><![CDATA[".$val."]]></".$key.">";
        }
        $xml .= "</xml>";
        return $xml;
    }

    public static function filterEmoji($str)
    {
        $len = mb_strlen($str);
        $new_text = '';
        for ($i = 0; $i < $len; $i++) {
            $word = mb_substr($str, $i, 1);
            if (strlen($word) <= 3) {
                $new_text .= $word;
            }
        }
        return $new_text;
    }

    public static function xml2array($xml)
    {
        libxml_disable_entity_loader(true);
        return json_decode(json_encode(simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA)), true);
    }

    public static function excel_dump($data,$titles=[],$title_txt='')
    {
        $str = "<html xmlns:o=\"urn:schemas-microsoft-com:office:office\"\r\nxmlns:x=\"urn:schemas-microsoft-com:office:excel\"\r\nxmlns=\"http://www.w3.org/TR/REC-html40\">\r\n<body>\r\n<table border=1>";
        if($title_txt)
        {
            $str .= "<tr>";
            $str .= "<th colspan='3'>{$title_txt}</th>";
            $str .= "</tr>";
        }
        if(!empty($titles))
        {
            $str .= "<tr>";
            foreach ($titles as $title)
            {
                $str .= "<th>{$title}</th>";
            }
            $str .= "</tr>";
        }
        foreach ($data as $row)
        {
            $str .= "\n\t<tr>";
            foreach ($row as $cell)
            {
                $str .= "\n\t\t<td>{$cell}</td>";
            }
            $str .= "\n\t</tr>";
        }

        $str .= "\n</table></body></html>";
        return $str;
    }

    public static function fake_mobile($mask = false)
    {
        $start = [
            //中国移动：
            134,135,136,137,138,139,150,151,152,157,158,159,165,170,172,178,182,183,184,187,188,195,197,198,
            //中国联通：
            130,131,132,155,156,166,167,170,171,175,176,185,186,196,
            //中国电信：
            133,134,153,162,170,173,174,177,180,181,189,190,191,193,199,
        ];
        $fake_mobile = strval($start[array_rand($start)]).sprintf('%08d',rand(9999,99999999));
        return $mask ? self::mask($fake_mobile,3,4) : $fake_mobile;
    }

}
