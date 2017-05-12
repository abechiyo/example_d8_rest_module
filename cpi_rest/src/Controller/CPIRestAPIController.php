<?php
/**
 * @file
 * Contains \Drupal\cpi_rest\Controller\CPIRestAPIController.
 */

namespace Drupal\cpi_rest\Controller;
use Drupal\node\Entity\Node;
use Drupal\Core\Entity;
use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Drupal\Core\Cache\CacheBackendInterface;

/**
 * Controller routines for cpi_rest routes.
 */
class CPIRestAPIController extends ControllerBase {

  /**
   * Callback for /rest/v1/node/{node} API method.
   * for Event data.
   */
  public function get_event_data( Request $request ) {

    ////////////////////////////////////
    // URLを取得
    ////////////////////////////////////
    $current_path = \Drupal::service('path.current')->getPath();
    /*
    URLをスラッシュ区切り
    Array
    (
        [0] => 
        [1] => rest
        [2] => v1
        [3] => node
        [4] => 1
    )
    */
    $path_args = explode('/', $current_path);

    // リクエストされた記事番号
    $node_id = htmlspecialchars($path_args[4], ENT_QUOTES, 'UTF-8');

    ////////////////////////////////////
    // キャッシュの利用
    // 記事が更新されたときや、新規追加時など、別途キャッシュをクリアする
    // 運用にするか、キャッシュクリアのモジュールを作成するかが必要
    ////////////////////////////////////
    $cid = 'cpi_rest_module:rest_node_id' . $node_id;

    // キャッシュがある場合は、キャッシュからリターン
    if ($cache = \Drupal::cache()->get($cid)) {
      return new JsonResponse( $cache->data );
    }

    ////////////////////////////////////
    // Nodeデータを取得
    ////////////////////////////////////
    $node = Node::load( $node_id );

    ////////////////////////////////////
    // 記事のステータスコードが1以外の場合、一般ユーザーは、
    // 記事を参照することができない
    ////////////////////////////////////
    if($node == ''){
          $response['response_code'] = 403;
          $response['error_message'] = 'Forbidden';
          return new JsonResponse( $response );
    }else if($node->get('status')->value != 1){
          $response['response_code'] = 403;
          $response['error_message'] = 'Forbidden';
          return new JsonResponse( $response );
    }


    ////////////////////////////////////
    // 様々な記事の情報からリレーションを行うコードを書く
    ////////////////////////////////////
    // e.g) ユーザー情報
    $node->field_user->getValue();
    // 「field_event_eye_catch」というカスタムフィールドを作成している場合
    $node->field_event_eye_catch->getValue()

    //////////////////////////////////// 
    // SQLを実行し、その他の記事を取得する例
    // 「field_event_tags」の「entity.name」に「タグの名前」が入っている
    // 記事を取得する
    ////////////////////////////////////
    $query = \Drupal::entityQuery('node')
      ->condition('status', 1)
      ->condition('field_event_tags.entity.name', 'タグの名前');
    $nids = $query->execute();

    // リターン用のJSONデータを $ersponseに格納していく
    $response['field_name'] = $node->get('field_event_name')->value;
    // 本文
    $response['body'] = $node->body->getValue();
    // ステータスコード
    $response['response_code'] = 200;
    
    ////////////////////////////////////
    // Return JSONデータ
    ////////////////////////////////////
    return new JsonResponse( $response );

  }

  /**
   * Callback for /rest/v1/user/{user_id} API method.
   * for User data.
   */
  public function get_user_data( Request $request ) {

    ////////////////////////////////////
    // URLからユーザー情報を取得
    ////////////////////////////////////
    $current_path = \Drupal::service('path.current')->getPath();
    /*
    URLをスラッシュ区切り
    Array
    (
        [0] => 
        [1] => rest
        [2] => v1
        [3] => user
        [4] => 1
    )
    */
    $path_args = explode('/', $current_path);

    // リクエストされたユーザーID
    htmlspecialchars($path_args[4], ENT_QUOTES, 'UTF-8')

    ////////////////////////////////////
    // Uesr情報を取得
    ////////////////////////////////////    
    $user_info = \Drupal\user\Entity\User::load( $user_id );

    ////////////////////////////////////
    // ステータスコードが1以外の場合、エラーを出力
    ////////////////////////////////////
    if($user_info == ''){
          $response['response_code'] = 403;
          $response['error_message'] = 'Forbidden';
          return new JsonResponse( $response );
    }else if($user_info->get('status')->value != 1){
          $response['response_code'] = 403;
          $response['error_message'] = 'Forbidden';
          return new JsonResponse( $response );
    }

    // ユーザー名
    $return_user['name'] = $user_info->get('name')->value ;

    return new JsonResponse( $return_user );
  }

  /**
   * PUT、POST、DELETE Sample code
   */

  /**
   * Callback for `my-api/put.json` API method.
   */
  public function put_example( Request $request ) {

    $response['data'] = 'Some test data to return';
    $response['method'] = 'PUT';

    return new JsonResponse( $response );
  }

  /**
   * Callback for `my-api/post.json` API method.
   */
  public function post_example( Request $request ) {

    // This condition checks the `Content-type` and makes sure to 
    // decode JSON string from the request body into array.
    if ( 0 === strpos( $request->headers->get( 'Content-Type' ), 'application/json' ) ) {
      $data = json_decode( $request->getContent(), TRUE );
      $request->request->replace( is_array( $data ) ? $data : [] );
    }

    $response['data'] = 'Some test data to return';
    $response['method'] = 'POST';

    return new JsonResponse( $response );
  }

  /**
   * Callback for `my-api/delete.json` API method.
   */
  public function delete_example( Request $request ) {

    $response['data'] = 'Some test data to return';
    $response['method'] = 'DELETE';

    return new JsonResponse( $response );
  }
}