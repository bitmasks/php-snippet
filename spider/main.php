<?php
ini_set('date.timezone', 'Asia/Shanghai');
/**
 * 基于QueryList的淘宝、京东、天猫商品采集
 * Created by PhpStorm.
 * User: taorong
 * Date: 2017/6/17
 * Time: 22:46
 *
 */
function test()
{
    $data = [
        'https://item.taobao.com/item.htm?spm=a1z10.1-c.w4004-706098327.8.nJ12IN&id=547085224449',
        'http://item.jd.com/10510046052.html',
        'http://item.jd.com/3133821.html',
        'http://item.jd.com/4654902.html',
        'https://detail.tmall.com/item.htm?spm=875.7931836/B.20161011.17.oUcfJY&abtest=_AB-LR845-PR845&pvid=e86b30c8-ee48-445b-b667-755cf161166e&pos=11&abbucket=_AB-M845_B1&acm=201509290.1003.1.1286473&id=44086295063&scm=1007.12710.81710.100200300000000&skuId=3210411003275',
        'https://detail.tmall.com/item.htm?id=535904716481&spm=875.7931836/B.20161011.18.oUcfJY&spm=875.7931836/B.20161011.18.oUcfJY&itemId=535904716481&itemId=535904716481&sellerId=305358018&sellerId=305358018&pvid=24944364-996e-4dc0-acc5-af05397040a9&pvid=24944364-996e-4dc0-acc5-af05397040a9&scm=1007.13088.38695.100200300000000&scm=1007.13088.38695.100200300000000&itemClk=&itemClk=',
        'https://detail.tmall.com/item.htm?spm=875.7931836/B.20161012.12.oUcfJY&abtest=_AB-LR845-PR845&pvid=7f4c2acd-e839-4e85-8bcf-525720391edc&pos=8&abbucket=_AB-M845_B1&acm=201509290.1003.1.1286473&id=20496648200&scm=1007.12710.81710.100200300000000',
        'https://detail.tmall.com/item.htm?spm=875.7931836/B.20161014.2.oUcfJY&abtest=_AB-LR845-PR845&pvid=2de5d9c2-5755-48dc-93fe-3cbbbbe0e04d&pos=18&abbucket=_AB-M845_B1&acm=201509290.1003.1.1286473&id=546197123660&scm=1007.12710.81710.100200300000000&sku_properties=5919063:6536025',
        'https://item.taobao.com/item.htm?spm=a21bo.50862.201875.75.q7hrn4&scm=1007.12493.69999.100200300000005&id=552434159855&pvid=04bdaf22-75d2-41c8-9a1e-56c3dbae452d',
        'https://item.taobao.com/item.htm?ft=t&spm=2013.1.20141001.3.JgYj76&id=527926128994&scm=1007.12144.81309.42296_0&pvid=5a6cef2f-c625-429a-aa10-69385c5bb3c8',
    ];
    //[ 'urls' => 'https://item.taobao.com/item.htm?spm=a1z10.1-c.w4004-706098327.8.nJ12IN&id=547085224449' ];

    $spider = new Spider();
    $res = $spider->main($data);
    print_r($res);

}

test();

class Spider
{
    public $html;
    public $timeout = 15;


    public function main($params)
    {
        $begin = date('H:i:s');
        if (!isset($params) || empty($params)) {
            return $this->exception('请输入要采集的链接');
        }

        if (is_array($params) && count($params) > 10) {
            return $this->exception('最多可导入10条');
        }


        $data = [
            'success' => [],
            'wrong' => [],
            'fail' => []
        ];
        if (is_array($params) && !empty($params)) {
            foreach ($params as $k => $v) {
                $res = $this->sence($v);
                if ($res > 0) {
                    $data['success'][] = $v;
                } else if ($res = -1) {
                    $data['wrong'][] = $v;
                } else {
                    $data['fail'][] = $v;
                }
            }
        } else if (!is_array($params) && !empty($params)) {
            $res = $this->sence($params);
            if ($res > 0) {
                $data['success'] = $params;
            } else if ($res = -1) {
                $data['wrong'] = $params;
            } else {
                $data['fail'] = $params;
            }
        }
        $end = date('H:i:s');
        $data['begin'] = $begin;
        $data['end'] = $end;
        return $this->json($data);

    }

    /**
     * 采集规则调度
     * @param string $url
     */
    private function sence($url = '')
    {
        if (preg_match('/http:\/\/item.jd.com/i', $url)) {
            return $this->jd($url);
        } else if (preg_match('/https:\/\/item.taobao.com/i', $url)) {
            return $this->taobao($url);
        } else if (preg_match('/https:\/\/detail.tmall.com/i', $url)) {
            return $this->tmall($url);
        } else {
            return -1;
        }
    }


    /**
     * 京东抓取规则
     */
    private function jd($url)
    {

        try {
            require './vendor/QueryList/vendor/autoload.php';
            //设置日志文件路径
            //\QL\QueryList::setLog('./log/ql.log');
            //采集html  1450634975   3133811
            $hj = \QL\QueryList::Query(
                $url,
                [
                    "detail" => array('.detail-content-wrap', 'html'),
                    "title" => array('title', 'html'),
                    "id" => array('a.follow.J-follow[data-id]', 'data-id'),
                ],
                '',
                'UTF-8',
                'GB2312'
            );


            //拿出skuid， 取json串，获得价格
            $html = $hj->getData(function ($x) {
                //拿出商品skuid，
                // 请求接口 http://p.3.cn/prices/mgets?callback=&type=&area=&pdtk=&pduid=&pdpin=&pdbp=&skuIds=J_1450634975&source=item-pc
                $jd_goods_price_api = 'http://p.3.cn/prices/mgets?callback=&type=&area=&pdtk=&pduid=&pdpin=&pdbp=&skuIds=J_';
                preg_match('/\[\{(.+)\}\]/', file_get_contents($jd_goods_price_api . $x['id'] . '&source=item-pc', false, stream_context_create(['http' => ['timeout' => $this->timeout]])), $json);
                $price_array = json_decode($json['0'], true);
                $x['price'] = $price_array['0']['op'];
                return $x;
            });
            $this->html = $html['0']['detail'];


            //采集描述中的图片
            $hj->setQuery(array(
                "img" => array('.detail-content-wrap img[data-lazyload]', 'data-lazyload')
            ));
            $hj->getData(function ($x) {
                $url = $this->save_pic($x['img']);
                $this->html = str_replace($x['img'], 'http://' . $_SERVER['SERVER_NAME'] . $url, $this->html);
                return $x['img'] = ['url' => 'http://' . $_SERVER['SERVER_NAME'] . $url, 'origin' => $x['img']];
            });
            $this->html = str_replace('data-lazyload', 'src', $this->html) . '<br/ >';

            // 采集主图
            $hj->setQuery(array(
                "image" => array('#spec-list img[data-url]', 'data-url')
            ));
            $main_imgs = $hj->getData(function ($x) {
                return [
                    'image' => $this->save_pic('http://img14.360buyimg.com/n0/' . $x['image']),
                    'origin' => 'http://img14.360buyimg.com/n0/' . $x['image']
                ];
            });


            //采集标题
            $html['0']['title'] = str_replace('【图片 价格 品牌 报价】-京东', '', $html['0']['title']);
            $html['0']['title'] = str_replace('【自营配送】', '', $html['0']['title']);
            //采集价格


            return $this->add_goods([
                'goods_name' => $html['0']['title'],
                'goods_price' => $html['0']['price'],
                'detail' => $this->html,
                'goods_images' => $main_imgs
            ]);

        } catch (\Exception $e) {
            return 0;
        }

    }


    /**
     * 淘宝抓取规则
     */
    private function taobao($url)
    {
        try {
            require './vendor/QueryList/vendor/autoload.php';
            //设置日志文件路径

            //采集html  1450634975   3133811
            $hj = \QL\QueryList::Query(
                $url,
                [
                    "detail" => array('head', 'html'),
                    "title" => array('#J_Title h3[data-title]', 'data-title'),
                    "price" => array('input[name="current_price"]', 'value'),
                ],
                '',
                null,
                'GB2312'
            );
            $html = $hj->getData(function ($x) {
                $desc_api_tmp = [[]];
                preg_match_all("/\/\/dsc.taobaocdn.com(.+)\\' /", $x['detail'], $desc_api_tmp);
                $url = 'http:' . str_replace('\'', '', $desc_api_tmp[0][0]);
                $desc_tmp = file_get_contents($url, false, stream_context_create(['http' => ['timeout' => $this->timeout]]));
                $this->html = $x['detail'] = str_replace(['\';', 'var desc=\''], '', $desc_tmp);
                return $x;
            });

            $html['0']['detail'] = iconv('gbk', 'UTF-8', $html['0']['detail']);

            //采集主图
            $hj->setQuery(array(
                "image" => array('#J_UlThumb img[data-src]', 'data-src'),
                "origin" => array('#J_UlThumb img[data-src]', 'data-src')
            ));
            $main_imgs = $hj->getData(function ($x) {
                $x['image'] = $this->save_pic(str_replace('_50x50.jpg', '', $x['image']));
                return $x;
            });


            return $this->add_goods([
                'goods_name' => $html['0']['title'],
                'goods_price' => $html['0']['price'],
                'detail' => $html['0']['detail'],
                'goods_images' => $main_imgs
            ]);
        } catch (\Exception $e) {
            return 0;
        }


    }


    /**
     * 天猫抓取规则
     */
    private function tmall($url)
    {
        try {
            require './vendor/QueryList/vendor/autoload.php';
            //设置日志文件路径

            //采集html  1450634975   3133811
            $hj = \QL\QueryList::Query(
                $url,
                [
                    "html" => array('body', 'html'),
                    "title" => array('input[name="title"]', 'value'),
                ],
                '',
                null,
                'GB2312'
            );


            $html = $hj->getData(function ($x) {

                $desc_api_tmp = [[]];
                preg_match_all("/\/\/dsc.taobaocdn.com(.+)\"\,\"fet/", $x['html'], $desc_api_tmp);
                $url = 'http:' . str_replace('","fet', '', $desc_api_tmp[0][0]);
                $desc_tmp = file_get_contents($url, false, stream_context_create(['http' => ['timeout' => $this->timeout]]));
                $this->html = $x['detail'] = str_replace(['\';', 'var desc=\''], '', $desc_tmp);
                preg_match_all("/defaultItemPrice\":\"(.+)\"\,\"double11/", $x['html'], $price_str_tmp);
                $x['price'] = str_replace(['defaultItemPrice":"', '","double11'], '', $price_str_tmp)[1][0];
                unset($x['html']);
                return $x;
            });

            $html['0']['detail'] = iconv('gbk', 'UTF-8', $html['0']['detail']);


            //采集主图
            $hj->setQuery(array(
                "image" => array('#J_UlThumb img[src]', 'src'),
                "origin" => array('#J_UlThumb img[src]', 'src')
            ));
            $main_imgs = $hj->getData(function ($x) {
                $x['image'] = $this->save_pic(str_replace('_50x50.jpg', '', $x['image']));
                return $x;
            });


            return $this->add_goods([
                'goods_name' => $html['0']['title'],
                'goods_price' => $html['0']['price'],
                'detail' => $html['0']['detail'],
                'goods_images' => $main_imgs
            ]);

        } catch (\Exception $e) {
            return 0;
        }


    }


    /**
     * 保存图片到本
     */
    private function save_pic($url)
    {
        if (!preg_match('/http:/i', $url)) {
            $url = 'http:' . $url;
        }
        $ext = explode('.', $url);
        $ext = '.' . $ext[count($ext) - 1];
        $name = rand(1, 9999) . $ext;
        $path = './uploads/' . date('Ymd');
        $this->mkdir($path);// 忽略file exits;
        $content = file_get_contents($url, false, stream_context_create(['http' => ['timeout' => $this->timeout]]));
        file_put_contents($path . '/' . $name, $content);
        return 'uploads/' . date('Ymd') . '/' . $name;
    }


    /**
     * 业务处理
     */
    private function add_goods($result)
    {

        // ........

        if (isset($result['detail']) && !empty($result['detail'])) {
            $path = './html/' . date('Ymd');
            $this->mkdir($path);
            file_put_contents($path . '/' . rand(1, 9999) . '.html', $result['detail']);
        }

        return $result;

    }

    private function mkdir($path)
    {
        //判断目录存在否
        if (is_dir($path)) {
            return 1;
        } else {
            //第三个参数是“true”表示能创建多级目录，iconv防止中文目录乱码
            $res = mkdir(iconv("UTF-8", "GBK", $path), 0777, true);
            if ($res) {
                return 1;
            } else {
                die("目录 $path 创建失败");
            }
        }
    }


    private function exception($msg)
    {
        exit(json_encode(['code' => 0, 'msg' => $msg], true));
    }


    private function json($data)
    {
        exit(json_encode($data, true));
    }

}