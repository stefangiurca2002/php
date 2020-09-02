<?php 
require '../DatabaseConnection.php';
$data = file_get_contents("php://input");
$data = json_decode($data);
$limit = $data->limit;
$userName = $data->userName;
$result = [];
try{
    $sql = "SELECT friendName FROM friends WHERE userName='$userName'";
    $friends = $conn->query($sql);
    while($row = $friends->fetch()){
        $friendNameEncoded = base64_encode($row['friendName']);
        $friendNameEncoded = str_replace('=','_',$friendNameEncoded);
        $sql = "SELECT userName, PostName, likes, dislikes, rate, date FROM Posts WHERE userName='$friendNameEncoded'";
        $friendsPosts = $conn->query($sql);
        while($post = $friendsPosts->fetch()){
            $encodedUserName = $post['userName'];
            $sql = "SELECT postName FROM usersIstoric WHERE userName='$encodedUserName'";
            $istoric = $conn->query($sql);
            $isViewed = false;
            while($istoricPost = $istoric->fetch()){
                $encodedPostName = str_replace('=','_',$post['PostName']);
                if($post['userName'].'/'.$encodedPostName == $istoricPost['postName']){
                    $isViewed = true;
                break;
                }
            }
            if(!$isViewed)
            array_push($result, $post);
        }
    }
    $nrPosts = 0;
    $ok = true;
    $chunckresult = [];
    while($ok && count($chunckresult)<$limit){
        $ok = false;
    for($i=1; $i<count($result) - $nrPosts; $i++){
        if($result[$i-1]['date'] > $result[$i]['date']){
            $ok = true;
            $aux = $result[$i-1];
            $result[$i-1] = $result[$i];
            $result[$i] = $aux;
        }
    }
        $postName = $result[count($result)-$nrPosts-1]['userName'].'/'.$result[count($result)-$nrPosts-1]['PostName'];
        $userPostName = $result[count($result)-$nrPosts-1]['userName'];
        if($userPostName && $postName)
        $conn->exec("INSERT INTO usersIstoric (userName, postName) VALUES ('$userPostName', '$postName')");
        array_push($chunckresult, $result[count($result)-$nrPosts-1]);
    $nrPosts++;
}
echo json_encode($chunckresult);
}
catch(PDOexception $e){
    echo json_encode(['mesage'=>$e->getMessage()]);
}