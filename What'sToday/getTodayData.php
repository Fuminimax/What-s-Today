<?php
    date_default_timezone_set('Asia/Tokyo');

    header("Content-Type: application/xml; charset=UTF-8");

    $url = sprintf('http://ja.wikipedia.org/w/api.php?action=query&export&format=txt&titles=%s',
        urlencode(getMonthDayArgs(urldecode($_GET['monthday']))));

    // 今日のトピックを取得
    $dayTopicText = file_get_contents($url);

    // 今日のトピックXMLを作成
    createDayTopicXML($dayTopicText, getMonthDayArgs(urldecode($_GET['monthday'])));

    // 月日の引数を取得します
    function getMonthDayArgs($args)
    {
    	global $argc;
    	global $argv;
    	
        // 引数が設定されていない場合
        if(!isset($args))
        {
            return date('n月j日');
        }
        
        // コマンドラインの引数として日付が設定されている場合
        if($argc != 1){
        	var_dump($argc);
        	return $argv[1] . '月' . $argv[2] . '日';
        }

        // 月日の形式ではない場合
        if(!preg_match('/(\d+).+?(\d+)/', mb_convert_encoding($args, "UTF-8", "ASCII,JIS,UTF-8,SJIS-win,eucJP-win"), $matches))
        {
            return date('n月j日');
        }

        if($matches[1] < 1 || 12 < $matches[1])
        {
            return date('n月j日');
        }

        switch($matches[1])
        {
            case '2':
                if($matches[2] < 1 || 29 < $matches[2])
                {
                    return date('n月j日');
                }

                break;
            case '4':
            case '6':
            case '9':
            case '11':
                if($matches[2] < 1 || 30 < $matches[2])
                {
                    return date('n月j日');
                }

                break;
            default:
                if($matches[2] < 1 || 31 < $matches[2])
                {
                    return date('n月j日');
                }
        }

        return $matches[1] . '月' . $matches[2] . '日';
    }

    // 今日のトピックXMLを作成します
    function createDayTopicXML($dayTopicText, $monthday)
    {
        // SimpleXMLをインスタンス化
        $rootNode = new SimpleXMLElement('<?xml version="1.0" encoding="UTF-8" standalone="yes"?><items></items>');

        // 今日のトピックを改行でスプリット
        $dayTopicArr = explode("\n", $dayTopicText);

        // 説明ノードを作成
        $i = createDescriptionNode($dayTopicArr, $monthday, $rootNode);

        // 出来事ノードを作成
        $i = createEventsNode($dayTopicArr, $i, $rootNode);

        // 誕生日項目を作成
        $i = createItem($dayTopicArr, $i, $rootNode->addChild('birthday'), '忌日');

        // 忌日項目を作成
        $i = createItem($dayTopicArr, $i, $rootNode->addChild('anniversary'), '記念日・年中行事');

        // 記念日を作成
        $i = createItem($dayTopicArr, $i, $rootNode->addChild('topic'), 'フィクションのできごと');
        
        $dom = new DOMDocument('1.0');
        $dom->loadXML($rootNode->asXML());
        $dom->formatOutput = true;
        file_put_contents('sampleData/' . $monthday . '.xml', $dom->saveXML());
        echo $dom->saveXML();
    }

    // 説明ノードを作成します
    function createDescriptionNode($dayTopicArr, $monthday, $rootNode)
    {
        // 配列の数を取得
        $arrCount = count($dayTopicArr);

        for($i = 0; $i < $arrCount; $i++)
        {            
            // 月日の説明ではない場合
            if(!preg_match(sprintf("/\'%s\'/", $monthday), $dayTopicArr[$i]))
            {
                continue;
            }

            // ルートノードに説明ノードを追加
            $descriptionNode = $rootNode->addChild('description');
            
            // 説明ノードに項目を追加
            $descriptionNode->addChild('item', removeExtraItems($dayTopicArr[$i]));
            break;
        }

        return $i;
    }

    // 出来事ノードを作成します
    function createEventsNode($dayTopicArr, $startIndex, $rootNode)
    {
        // 配列の数を取得
        $arrCount = count($dayTopicArr);

        for($i = $startIndex; $i < $arrCount; $i++)
        {
            // できごとではない場合
            if(!preg_match('/\=\= できごと \=\=/', $dayTopicArr[$i]))
            {
                continue;
            }

            break;
        }

        return createItem($dayTopicArr, $i, $rootNode->addChild('events'), '誕生日');
    }

    // 項目を作成します
    function createItem($dayTopicArr, $startIndex, $node, $exitTitle)
    {
        // 配列の数を取得
        $arrCount = count($dayTopicArr);

        for($i = $startIndex; $i < $arrCount; $i++)
        {
            // 終了条件のタイトルの場合
            if(preg_match(sprintf('/\=\= %s \=\=/', $exitTitle), $dayTopicArr[$i]))
            {
                break;
            }

            // 有効な行ではない場合
            if(!preg_match('/^\*.+/', $dayTopicArr[$i]))
            {
                continue;
            }

            $ret = removeExtraItems($dayTopicArr[$i]);
            if($ret){
            	$node->addChild('item', $ret);
            }
        }

        return $i;
    }

    // 余計な項目を削除します
    function removeExtraItems($item)
    {
        // 出典を削除
        $result = preg_replace('/\{\{.+?\}\}/', '', $item);

        // マルチワードを削除
        $result = preg_replace_callback('/\[\[(.+?)\]\]/', removeMultiWord, $result);

        // &lt; ～ &gt;を削除
        $result = preg_replace('/\&lt\;.+?\&gt\;/', '', $result);

        // 「en:」を削除
        $result = preg_replace('/en\:/', '', $result);

        // 先頭の「* 」を削除
        $result = preg_replace('/^\* ?/', '', $result);

        // 記号を削除
        $result = preg_replace('/[\'\[\]]/', '', $result);
        
        // （）を削除
        $result = preg_replace('/（）/', '', $result);
        
        // 先頭の「: 」を削除
        $result = preg_replace('/^\: ?/', '', $result);

        return $result;
    }

    // マルチワードを削除します
    function removeMultiWord($m)
    {
        return preg_replace('/.+\|/', '', $m[1]);
    }
?>