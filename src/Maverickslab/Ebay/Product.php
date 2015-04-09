<?php
    /**
     * Created by PhpStorm.
     * User: Optimistic
     * Date: 18/03/2015
     * Time: 18:29
     */

    namespace Maverickslab\Ebay;


    class Product
    {
        use InjectAPIRequester;

        public function get($user_token, $product_id=null){
            if(is_null($product_id))
                return self::all($user_token);

            return self::one($user_token,$product_id);
        }

        /**
         * Import products in batches
         * @param     $user_token
         * @param int $site_id
         * @param int $page
         *
         * @return mixed
         * @throws \Exception
         */
        public function all($user_token, $site_id = 1, $page = 1)
        {
            $entries_per_page = config('ebay.entries_per_page');

            $inputs = [];
            $inputs['RequesterCredentials'] = [
                'eBayAuthToken' => $user_token
            ];

            $inputs['ActiveList'] = [
                'Include'    => true,
                'Pagination' => [
                    'EntriesPerPage' => $entries_per_page,
                    'PageNumber'     => $page
                ]
            ];

            return $this->requester->request($inputs, 'GetMyeBaySelling', $site_id);
        }

        /**
         * import an individual product
         * @param $user_token
         * @param $item_id
         *
         * @return mixed
         * @throws \Exception
         */
        public function one($user_token, $item_id){
            $inputs = [];
            $inputs['RequesterCredentials'] = [
                'eBayAuthToken'=>$user_token
            ];
            $inputs['ItemID']=[
                $item_id
            ];
            $inputs['DetailLevel']=[
                'ReturnAll'
            ];

            return $this->requester->request($inputs,'GetItem');
        }
    }