<?php
/**
 * Created by PhpStorm.
 * User: Thpffcj
 * Date: 2017/9/6
 * Time: 21:41
 */
/**
 * 添加商品
 * @return string
 */
function addPro($link){
    $arr=$_POST;
    $arr['pubTime']=time();
    $path="./uploads";
    $uploadFiles=uploadFile($path);
    if(is_array($uploadFiles)&&$uploadFiles){
        foreach($uploadFiles as $key=>$uploadFile){
            thumb($path."/".$uploadFile['name'],"../image_50/".$uploadFile['name'],50,50);
            thumb($path."/".$uploadFile['name'],"../image_220/".$uploadFile['name'],220,220);
            thumb($path."/".$uploadFile['name'],"../image_350/".$uploadFile['name'],350,350);
            thumb($path."/".$uploadFile['name'],"../image_800/".$uploadFile['name'],800,800);
        }
    }
    $res=insert("imooc_pro",$arr,$link);
    $pid=getInsertId($link);
    if($res&&$pid){
        foreach($uploadFiles as $uploadFile){
            $arr1['pid']=$pid;
            $arr1['albumPath']=$uploadFile['name'];
            addAlbum($link, $arr1);
        }
        $mes="<p>添加成功!</p><a href='addPro.php' target='mainFrame'>继续添加</a>|<a href='listPro.php' target='mainFrame'>查看商品列表</a>";
    }else{
        foreach($uploadFiles as $uploadFile){
            if(file_exists("../image_800/".$uploadFile['name'])){
                unlink("../image_800/".$uploadFile['name']);
            }
            if(file_exists("../image_50/".$uploadFile['name'])){
                unlink("../image_50/".$uploadFile['name']);
            }
            if(file_exists("../image_220/".$uploadFile['name'])){
                unlink("../image_220/".$uploadFile['name']);
            }
            if(file_exists("../image_350/".$uploadFile['name'])){
                unlink("../image_350/".$uploadFile['name']);
            }
        }
        $mes="<p>添加失败!</p><a href='addPro.php' target='mainFrame'>重新添加</a>";

    }
    return $mes;
}

/**
 *编辑商品
 * @param int $id
 * @return string
 */
function editPro($link, $id){
    $arr=$_POST;
    $path="./uploads";
    $uploadFiles=uploadFile($path);
    if(is_array($uploadFiles)&&$uploadFiles){
        foreach($uploadFiles as $key=>$uploadFile){
            thumb($path."/".$uploadFile['name'],"../image_50/".$uploadFile['name'],50,50);
            thumb($path."/".$uploadFile['name'],"../image_220/".$uploadFile['name'],220,220);
            thumb($path."/".$uploadFile['name'],"../image_350/".$uploadFile['name'],350,350);
            thumb($path."/".$uploadFile['name'],"../image_800/".$uploadFile['name'],800,800);
        }
    }
    $where="id={$id}";
    $res=update($link, "imooc_pro",$arr,$where);
    $pid=$id;
    if($res&&$pid){
        if($uploadFiles &&is_array($uploadFiles)){
            foreach($uploadFiles as $uploadFile){
                $arr1['pid']=$pid;
                $arr1['albumPath']=$uploadFile['name'];
                addAlbum($link, $arr1);
            }
        }
        $mes="<p>编辑成功!</p><a href='listPro.php' target='mainFrame'>查看商品列表</a>";
    }else{
        if(is_array($uploadFiles)&&$uploadFiles){
            foreach($uploadFiles as $uploadFile){
                if(file_exists("../image_800/".$uploadFile['name'])){
                    unlink("../image_800/".$uploadFile['name']);
                }
                if(file_exists("../image_50/".$uploadFile['name'])){
                    unlink("../image_50/".$uploadFile['name']);
                }
                if(file_exists("../image_220/".$uploadFile['name'])){
                    unlink("../image_220/".$uploadFile['name']);
                }
                if(file_exists("../image_350/".$uploadFile['name'])){
                    unlink("../image_350/".$uploadFile['name']);
                }
            }
        }
        $mes="<p>编辑失败!</p><a href='listPro.php' target='mainFrame'>重新编辑</a>";

    }
    return $mes;
}

function delPro($link, $id){
    $where="id=$id";
    $res=delete("imooc_pro",$where);
    $proImgs=getAllImgByProId($link, $id);
    if($proImgs&&is_array($proImgs)){
        foreach($proImgs as $proImg){
            if(file_exists("uploads/".$proImg['albumPath'])){
                unlink("uploads/".$proImg['albumPath']);
            }
            if(file_exists("../image_50/".$proImg['albumPath'])){
                unlink("../image_50/".$proImg['albumPath']);
            }
            if(file_exists("../image_220/".$proImg['albumPath'])){
                unlink("../image_220/".$proImg['albumPath']);
            }
            if(file_exists("../image_350/".$proImg['albumPath'])){
                unlink("../image_350/".$proImg['albumPath']);
            }
            if(file_exists("../image_800/".$proImg['albumPath'])){
                unlink("../image_800/".$proImg['albumPath']);
            }

        }
    }
    $where1="pid={$id}";
    $res1=delete("imooc_album",$where1);
    if($res&&$res1){
        $mes="删除成功!<br/><a href='listPro.php' target='mainFrame'>查看商品列表</a>";
    }else{
        $mes="删除失败!<br/><a href='listPro.php' target='mainFrame'>重新删除</a>";
    }
    return $mes;
}

/**
 * 得到商品的所有信息
 * @return array
 */
function getAllProByAdmin($link){
    $sql="select p.id,p.pName,p.pSn,p.pNum,p.mPrice,p.iPrice,p.pDesc,p.pubTime,p.isShow,p.isHot,c.cName from imooc_pro as p join imooc_cate c on p.cId=c.id";
    $rows=fetchAll($sql, $link);
    return $rows;
}

/**
 *根据商品id得到商品图片
 * @param int $id
 * @return array
 */
function getAllImgByProId($link, $id){
    $sql="select a.albumPath from imooc_album a where pid={$id}";
    $rows=fetchAll($sql, $link);
    return $rows;
}

/**
 * 根据id得到商品的详细信息
 * @param int $id
 * @return array
 */
function getProById($link, $id){
    $sql="select p.id,p.pName,p.pSn,p.pNum,p.mPrice,p.iPrice,p.pDesc,p.pubTime,p.isShow,p.isHot,c.cName,p.cId from imooc_pro as p join imooc_cate c on p.cId=c.id where p.id={$id}";
    $row=fetchOne($link, $sql);
    return $row;
}