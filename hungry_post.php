<?php 

/*

Plugin Name: Hungry_POST
Author:
Plugin URI:
Description:
Version: 1.0
Author URI:
Domain Path:
Text Domain:

Creation Date: 2016/08/22
Modified: 

*/

add_action( 'admin_menu', 'hungry_post_menu' );

function hungry_post_menu() {
//ここにメニューを追加するためのの処理を記述
  add_menu_page( //codexを参照する
      __('Hungry POST', 'hungry--admin'),// titleタグに入る
      __('Hungry POST', 'hungry--admin'),// 管理画面左メニュータイトル
      'administrator',// 閲覧・使用の為の権限レベル管理者以外NG
      'hungry-post-admin',
      'hungry_post_admin'
  );

    add_submenu_page( //codexを参照する
        'hungry-post-admin',
      __('Hungry POST', 'hungry-post-admin'),// titleタグに入る
      'administrator',// 閲覧・使用の為の権限レベル管理者以外NG
      'hungry-post-sub-admin',
      'hungry_post_sub_admin'
  );

}

//new Hungry_add_action();//add_actionを実行するクラスのインスタンス

function hungry_post_admin(){
?>
    <!-- カレンダーのための記述 -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>
    <script src="http://ajax.googleapis.com/ajax/libs/jqueryui/1/jquery-ui.min.js"></script>
    <script src="http://ajax.googleapis.com/ajax/libs/jqueryui/1/i18n/jquery.ui.datepicker-ja.min.js"></script>
    <link rel="stylesheet" href="http://ajax.googleapis.com/ajax/libs/jqueryui/1/themes/overcast/jquery-ui.css" >
    <script>
        $(function() {
          $("#datepicker").datepicker();
        });
    </script>
    <!-- カレンダーのための記述 -->

    <?php

      $h_post_json = file_get_contents( ABSPATH . '../../json/hungry_immoral.json');
      $h_post_json = mb_convert_encoding($h_post_json , 'UTF8', 'ASCII,JIS,UTF-8,EUC-JP,SJIS-WIN');
      $h_post_json = json_decode( $h_post_json , true );

      $site_name = $_POST['to_sitename'];
      $the_date = $_POST['the_date'];
      $the_date_after = $_POST['the_date_after'];
      $post_go = $_POST['post_go'];
    ?>




    <div class="wrap">

    <h2>Hungry post 管理画面</h2>
    <p>
    <form action="" method="post" >
    <?php if($the_date){ ?>
    <input type="text" id="datepicker" name="the_date" value="<?php echo $the_date; ?>">
    <?php }else{ ?>
    <input type="text" id="datepicker" name="the_date" value="<?php echo $the_date_after; ?>">
    <?php } ?>
    <?php wp_nonce_field( 'my-nonce-key', 'hungry-post-admin' ); // CSRF対策 ?>
    <input type="hidden" name="set" value="hungry_post">
    <select id="to_sitename" name="to_sitename">

      <?php

        foreach ($h_post_json as $key => $value) {

          if($site_name == $key){
            echo '<option selected value="'.$key.'">'.$key.'</option>' ;
          }else{
            echo '<option value="'.$key.'">'.$key.'</option>' ;
          }

        }

      ?>
    </select>
    <input type="submit" value=" 記事表示 " class="button button-primary">
    </form>
    </p>

<?php

    if($the_date){
        postlist($the_date , $site_name);
    }

    if($post_go){
        postlist_go($post_go , $site_name);
    }


?>

    </div><!--wrap -->
<?php

}//hungry_cron_admin

function postlist($the_date , $site_name){//日付から取得記事一覧を出す

    $year = mb_substr($the_date ,0,4);
    $month = mb_substr($the_date ,5,2);
    $day = mb_substr($the_date ,8,2);

    $args = array(
        'post_type' => 'post',
        'date_query' => array(
           array(
             'year'  => $year,
             'month' => $month,
             'day'   => $day,
           ),
        ),
        'meta_query' => array(
            array(
              'key'     => 'status',
              'value'   => array( 0 ),
              'compare' => 'IN',
            ),
            array(
              'key' => 'site_name',
              'value' => $site_name,
              'compare' => '='
            )
        ),
    );

    $the_query = new WP_Query( $args );

    if ( $the_query->have_posts() ) :
        echo '<form action="" method="post" >';
        if( $site_name == "サイト1" ){
          echo '<input type="hidden" name="to_site_xmlrpc" value="http://www.site1.com/xmlrpc.php" />';
        }elseif( $site_name == "サイト2" ){
          echo '<input type="hidden" name="to_site_xmlrpc" value="http://www.site2.com/xmlrpc.php" />';
        }elseif( $site_name == "サイト3" ){
          echo '<input type="hidden" name="to_site_xmlrpc" value="http://www.site3.com/xmlrpc.php" />';
        }elseif( $site_name == "サイト4" ){
          echo '<input type="hidden" name="to_site_xmlrpc" value="http://www.site4.com/xmlrpc.php" />';
        }elseif( $site_name == "サイト5" ){
          echo '<input type="hidden" name="to_site_xmlrpc" value="http://www.site5.com/xmlrpc.php" />';
        }elseif( $site_name == "サイト6" ){
          echo '<input type="hidden" name="to_site_xmlrpc" value="http://www.site6.com/xmlrpc.php" />';
        }
        echo '<input type="hidden" name="to_sitename" value="'. $site_name .'" />' ;
        echo '<input type="hidden" name="the_date_after" value="'. $the_date .'" />' ;
        echo '<p><input type="submit" value=" 記事を'. $site_name .'へ投稿 " class="button button-primary" onclick="disabled = true;"></p>';

    while ( $the_query->have_posts() ) : $the_query->the_post();

              $post = null;
              $custom = get_post_custom($post->ID);
              $post_id = get_the_ID();

              echo '<ul>';
              echo '<li><input type="checkbox" name="post_go[]" value="'. $post_id .'" checked> '. $custom['site_name'][0] .' ： '. get_the_title() . '</li>';
              echo '</ul>';

    endwhile;
        echo '</form>';
      wp_reset_postdata();
    endif;

}


function postlist_go($post_go , $site_name){

    $h_post_json = file_get_contents( ABSPATH . '../../json/hungry_immoral.json');
    $h_post_json = mb_convert_encoding($h_post_json , 'UTF8', 'ASCII,JIS,UTF-8,EUC-JP,SJIS-WIN');
    $h_post_json = json_decode( $h_post_json , true );

    require_once 'IXR_Library.php';

    //ワードプレスURLをセット
    $client = new IXR_Client($_POST['to_site_xmlrpc']);

    $wp_user_id = $h_post_json[$site_name]['wp_user_id'];

    $args = array(
      'post_type' => 'post',
      'post__in' => $post_go,
    );

    $the_query = new WP_Query( $args );

    if ( $the_query->have_posts() ) :

    while ( $the_query->have_posts() ) : $the_query->the_post();

              $post = null;
              $custom = get_post_custom($post->ID);
              $post_id = get_the_ID();

              //投稿パラメータセット
              $post_type = "wp.newPost"; //投稿タイプ：新規投稿
              $blog_id = 1;              //blog ID: 通常は１
              $user_name = $h_post_json[$site_name]['wp_user_name']; //ユーザー名
              $password = $h_post_json[$site_name]['wp_user_pass'];  //パスワード
              $post_author = $h_post_json[$site_name]['wp_user_id']; //投稿者ID

              $post_info = get_post( get_the_ID() );
              $post_name_slug = $post_info->post_name;
              $post_slug = $post_name_slug;
              
              $post_date = get_post_time() - 32400 ;

              $year = date("Y",$post_date); // "Y/m/d H:i"
              $month = date("m",$post_date); // "Y/m/d H:i"
              $day = date("d",$post_date); // "Y/m/d H:i"
              $hour = date("H",$post_date); // "Y/m/d H:i"
              $minute = date("i",$post_date); // "Y/m/d H:i"

              $post_date = $year.$month.$day.'T'.$hour.':'.$minute.':00Z';

              $post_date = new IXR_Date( $post_date );

              $post_status = "future"; //投稿状態（future:公開予定 publish:公開済み）
              $post_title = get_the_title(); //記事タイトル

              $posttag_name = array();
              $posttags = get_the_tags(); //タグの全情報を取得
              foreach ( $posttags as $tag ) {
                $posttag_name[] = $tag->name ; //名前だけ取得して配列に格納
              }

              $category_name = array();
              $categorys = get_the_category(); //カテゴリの全情報を取得
              foreach ( $categorys as $category ) {
                $category_name[] = $category->name ; //名前だけ取得して配列に格納
              }

              //投稿
              $status = $client->query(
                $post_type, $blog_id, $user_name, $password,
                array(
                  'post_type' => 'post',
                  'post_name' => $post_slug,
                  'post_author' => $post_author,
                  'post_date' => $post_date,
                  'post_status' => $post_status,
                  'post_title' => $post_title,
                  'terms_names' => array('category' => $category_name, 'post_tag' => $posttag_name),
                  'custom_fields' => array(
                    array('key' => 'meta_title', 'value' => $custom['meta_title'][0]),
                    array('key' => 'meta_keywords', 'value' => $custom['meta_keywords'][0]),
                    array('key' => 'meta_description', 'value' =>  $custom['meta_description'][0]),
                    array('key' => 'video_thumbnail', 'value' => $custom['video_thumbnail'][0]),
                    array('key' => 'video_url', 'value' => $custom['video_url'][0]),
                    array('key' => 'dead_link', 'value' => $custom['dead_link'][0]),
                    array('key' => 'dmm_title', 'value' => $custom['dmm_title'][0]),
                    array('key' => 'dmm_url', 'value' => $custom['dmm_url'][0]),
                    array('key' => 'dmm_imgurl', 'value' => $custom['dmm_imgurl'][0]),
                    array('key' => 'maker', 'value' => $custom['maker'][0]),
                    array('key' => 'video_url2', 'value' => $custom['video_url2'][0]),
                    array('key' => 'dead_link2', 'value' => $custom['dead_link2'][0])
                  )
                )
              );

              if (!$status){
                  echo 'Error occured during category request.' . $client->getErrorCode().":".$client->getErrorMessage();
              }else{
                  //echo $client->getResponse();
              }

              $back_path = $custom['back_img_path'][0] ;
              $front_path = $custom['front_img_path'][0] ;

              update_post_meta($post_id, 'status',+1); // キーが「status」のカスタムフィールドの値にプラス1をする

              if( $site_name == "サイト1" ){
                 $image_url = 'http://www.site1.com/' . $back_path ;
              }elseif( $site_name == "サイト2" ){
                $image_url = 'http://www.site2.com/' . $back_path ;
              }elseif( $site_name == "サイト3" ){
                $image_url = 'http://www.site3.com/' . $back_path ;
              }elseif( $site_name == "サイト4" ){
                $image_url = 'http://www.site4.com/' . $back_path ;
              }elseif( $site_name == "サイト5" ){
                $image_url = 'http://www.site5.com/' . $back_path ;
              }
              /* http://ysklog.net/php/2370.html 参照 画像チェックしてアップロードするか判断 */

              //「$http_response_header」の初期化
              $http_response_header = array();

              //file_get_contents関数でデータを取得
              $data = @file_get_contents($image_url);
               
              if($http_response_header[0] == 'HTTP/1.1 200 OK'){
                echo '画像はすでに存在します。<br />';
              }else{
                hp_image_upload( $site_name , $back_path , $front_path ); //画像ftp投稿
                echo '画像が存在しませんでしたので、アップロードしました。<br />';
              }

              /* http://ysklog.net/php/2370.html 参照 画像チェックしてアップロードするか判断 */


              echo '<ul>';
              echo '<li>サイト名：' . $site_name . '：' .get_the_title()  . '</li>';
              echo '</ul>';

    endwhile;
      wp_reset_postdata();
    endif;
    echo '<p>以上の記事を投稿しました。</p>';
}


function hp_image_upload( $site_name , $back_path , $front_path ){//画像ftp投稿

              $h_post_json = file_get_contents( ABSPATH . '../../json/hungry_immoral.json');
              $h_post_json = mb_convert_encoding($h_post_json , 'UTF8', 'ASCII,JIS,UTF-8,EUC-JP,SJIS-WIN');
              $h_post_json = json_decode( $h_post_json , true );

              preg_match('/(.*)\//', $front_path , $make_dir_day) ;
              preg_match('/(.*)\//',$make_dir_day[1] , $make_dir_month) ;
              preg_match('/(.*)\//',$make_dir_month[1] , $make_dir_year) ;

              $ftpValue = array(
                  'ftp_server' => $h_post_json[$site_name]['ftp_server'],
                  'ftp_user_name' => $h_post_json[$site_name]['ftp_user_name'],
                  'ftp_user_pass' => $h_post_json[$site_name]['ftp_user_pass']
              );

              $remote_file = $front_path ;
              //ftpログインからの相対パスでなければならぬ  参考：http://d.hatena.ne.jp/takigawa401/20150427/1430121843

              $upload_file = ABSPATH . $back_path ;

              $connection = ftp_connect($ftpValue['ftp_server']);
              $login_result = ftp_login(
                  $connection,
                  $ftpValue['ftp_user_name'],
                  $ftpValue['ftp_user_pass']
              );

              ftp_pasv($connection, true);
              // エラー出力しない場合
              ini_set('display_errors', 0); //エラー出力しない
              ftp_mkdir($connection , $make_dir_year[1]);
              ftp_mkdir($connection , $make_dir_month[1]);
              ftp_mkdir($connection , $make_dir_day[1]);

              $ftpResult = ftp_put($connection, $remote_file, $upload_file, FTP_BINARY, false);

              if (!$ftpResult) {
                  throw new InternalErrorException('Something went wrong.');
              }

              ftp_close($connection);

}
