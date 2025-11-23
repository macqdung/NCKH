<?php
include('../MODEL/modelreview.php');

class control_review {
    private $model;

    public function __construct() {
        $this->model = new data_review();
    }

    public function submit_review($product_id, $user_id, $rating, $comment, $images = []) {
        if (!$this->model->has_user_purchased_product($user_id, $product_id)) {
            return ['success' => false, 'message' => 'この製品を購入したユーザーのみがレビューできます。'];
        }
        if ($this->model->has_user_reviewed_product($user_id, $product_id)) {
            return ['success' => false, 'message' => 'この製品に対してすでにレビューを投稿しています。'];
        }
        if ($this->model->insert_review($product_id, $user_id, $rating, $comment, $images)) {
            return ['success' => true, 'message' => 'レビューが正常に投稿されました。'];
        } else {
            return ['success' => false, 'message' => 'レビューの投稿に失敗しました。'];
        }
    }

    public function get_reviews($product_id) {
        return $this->model->get_reviews_by_product($product_id);
    }
}
?>
