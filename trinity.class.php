<?php

Namespace Trinity;

class TV
{
    const PARTNERID = 'XXX';
    const SALT = 'XXXXXXXXXX';
    const TRINITY_HOST = 'http://partners.trinity-tv.net';
    
    public function __construct() {
        
    }
    
    
    /*
     * Create user at TrinityTV DB
     * 
     * @param int $localid Identificator of user at your net
     * @param int $subscrid Identificator of TrinityTV tariff plan
     * 
     * @return array ('error'=>0/1, 'msg'=>'error message or empty')
     * 
     */
    public static function UserCreate($localid=0, $subscrid=0)
    {
        // GET /partners/user/create?requestid={requestid}&partnerid={partnerid}&localid={localid}&subscrid={subscrid}&hash={hash}
        // hash = md5(requestid+partnerid+localid+subscrid+salt)
        
        if ($localid == 0 || empty($localid) || !is_numeric($localid) || (int)($localid)<1){
            return array('error'=>1,'msg'=>'No local user ID entered');
        }
        if ($subscrid == 0 || empty($subscrid) || !is_numeric($subscrid) || (int)($subscrid)<1){
            return array('error'=>1,'msg'=>'No Subscriber ID entered (Can\'t set tariff plan)');
        }
        
        $int = self::GenInt();
        $hash = md5($int.self::PARTNERID.$localid.$subscrid.self::SALT);
        $uri = TRINITY_HOST.'/partners/user/create?requestid='.$int.'&partnerid='.Self::PARTNERID.'&localid='.$localid.'&subscrid='.$subscrid.'&hash='.$hash;
        $res = self::_get_uri($uri);
        
        $js = json_decode($res);
        
        if (isset($js->requestid) && $js->requestid == $int && $js->result == 'success'){
            return array(
                'error' => 0,
                'msg'   => ''
            );
        }
        else {
            return array(
                'error' => 1,
                'msg'   => 'Something went wrong: '.$js->result
            );
        }
    }
    
    
    /*
     * Edit user data at TrinityTV DB
     * 
     * @param int $localid Identificator of user at your net
     * @param string $lastname Last name of user at your net
     * @param string $firstname First name of user at your net
     * @param string $middlename Second name of user at your net
     * @param string $address user address at your net
     * 
     * @return array ('error'=>0/1, 'msg'=>'error message or empty')
     * 
     */
    public static function UserUpdate($localid=0,$lastname='',$firstname='',$middlename='',$address='')
    {
        // GET /partners/user/updateuser?requestid={requestid}&partnerid={partnerid}&localid={localid}firstname={partnerid}&lastname={lastname}&middlename={middlename}&address={address}&hash={hash}
        // hash = md5(requestid+partnerid+localid+firstname+lastname+middlename+address+salt)
        
        if ($localid == 0 || empty($localid) || !is_numeric($localid) || (int)($localid)<1){
            return array('error'=>1,'msg'=>'No local user ID entered');
        }
        
        $int = self::GenInt();
        $firstname=urlencode($firstname);
        $lastname=urlencode($lastname);
        $middlename=urlencode($middlename);
        $address=urlencode($address);
        $hash = md5($int.self::PARTNERID.$localid.$firstname.$lastname.$middlename.$address.self::SALT);
        $uri = TRINITY_HOST.'/partners/user/updateuser?requestid='.$int.'&partnerid='.Self::PARTNERID.'&localid='.$localid.'&lastname='.$lastname.'&firstname='.$firstname.'&middlename='.$middlename.'&address='.$address.'&hash='.$hash;
        $res = self::_get_uri($uri);
        
        $js = json_decode($res);

        if (isset($js->requestid) && $js->requestid == $int && $js->result == 'success'){
            return array(
                'error' => 0,
                'msg'   => ''
            );
        }
        else {
            return array(
                'error' => 1,
                'msg'   => 'Something went wrong: '.$js->result
            );
        }
    }
    
    
    /*
     * Edit users subscription
     * 
     * @param int $localid Identificator of user at your net
     * @param string $operationid Operation (suspend, resume, unsubscribe)
     * 
     * @return array ('error'=>0/1, 'msg'=>'error message or empty')
     * 
     */
    public static function Subscription($localid=0,$operationid='')
    {
        // GET /partners/user/subscription?requestid={requestid}&partnerid={partnerid}&localid={localid}&operationid={unsubscribe}&hash={hash}
        // operationid - unsubscribe ­ отключение подписки
        // operationid - suspend ­ приостановление подписки
        // operationid - resume ­ продолжение приостановленной подписки
        
        if ($localid == 0 || empty($localid) || !is_numeric($localid) || (int)($localid)<1){
            return array('error'=>1,'msg'=>'No local user ID entered');
        }
        
        if (!in_array($operationid,array('unsubscribe','suspend','resume'))){
            return array('error'=>1,'msg'=>'No operation entered (unsubscribe, suspend, resume)');
        }
        
        $int = self::GenInt();
        $hash = md5($int.self::PARTNERID.$localid.$operationid.self::SALT);
        $uri = TRINITY_HOST.'/partners/user/subscription?requestid='.$int.'&partnerid='.Self::PARTNERID.'&localid='.$localid.'&operationid='.$operationid.'&hash='.$hash;
        $res = self::_get_uri($uri);
        
        $js = json_decode($res);
        
        if (isset($js->requestid) && $js->requestid == $int && $js->result == 'success'){
            return array(
                'error' => 0,
                'msg'   => ''
            );
        }
        else {
            return array(
                'error' => 1,
                'msg'   => 'Something went wrong: '.$js->result
            );
        }
        
    }
    
    
    /*
     * Get info about user subscriptions
     * 
     * @param int $localid Identificator of user at your net
     * 
     * @return array ('error'=>0/1, 'msg'=>'error message or array of subscriptions')
     * 
     */
    public static function SubscriptionInfo($localid=0)
    {
        if ($localid == 0 || empty($localid) || !is_numeric($localid) || (int)($localid)<1){
            return array('error'=>1,'msg'=>'No local user ID entered');
        }
        
        // /partners/user/subscriptioninfo?requestid={requestid}&partnerid={partnerid}&localid={localid}&hash={hash}
        // hash = md5(requestid+partnerid+localid+salt)
        
        $int = self::GenInt();
        $hash = md5($int.self::PARTNERID.$localid.self::SALT);
        $uri = TRINITY_HOST.'/partners/user/subscriptioninfo?requestid='.$int.'&partnerid='.Self::PARTNERID.'&localid='.$localid.'&hash='.$hash;
        $res = self::_get_uri($uri);

        $js = json_decode($res);
        
        if (isset($js->requestid) && $js->requestid == $int && $js->result == 'success'){
            return array(
                'error' => 0,
                'msg'   => (array)$js->subscriptions
            );
        }
        else {
            return array(
                'error' => 1,
                'msg'   => 'Something went wrong: '.$js->result
            );
        }
    }
    
    
    /*
     * Add MAC address to subscription
     * 
     * @param int $localid Identificator of user at your net
     * @param string $mac Users MAC address at your net (00:00:00:00:00:00, 00-00-00-00-00-00, 000000000000)
     * 
     * @return array ('error'=>0/1, 'msg'=>'error message or empty')
     * 
     */
    public static function MACadd($localid=0,$mac='')
    {
        // old (till 2017.12.15) GET /partners/user/autorizemac?requestid={requestid}&partnerid={partnerid}&localid={localid}&mac={mac}&hash={hash}
        // GET /partners/user/autorizedevice?requestid={requestid}&partnerid={partnerid}&localid={localid}&mac={mac}&uuid={uuid}&hash={hash}
        // hash = md5(requestid+partnerid+localid+mac+salt)
        
        if ($localid == 0 || empty($localid) || !is_numeric($localid) || (int)($localid)<1){
            return array('error'=>1,'msg'=>'No local user ID entered');
        }
        
        if (
            empty($mac) || 
            !preg_match('#^\w\w[:-]?\w\w[:-]?\w\w[:-]?\w\w[:-]?\w\w[:-]?\w\w$#uis',$mac)
        ){
            return array('error'=>1,'msg'=>'No valid user MAC address entered');
        }
        
        $mac = str_replace('-','',$mac);
        $mac = str_replace(':','',$mac);
        $mac = strtoupper($mac);
        
        $int = self::GenInt();
        $hash = md5($int.self::PARTNERID.$localid.$mac.self::SALT);
        // $uri = TRINITY_HOST.'/partners/user/autorizemac?requestid='.$int.'&partnerid='.Self::PARTNERID.'&localid='.$localid.'&mac='.$mac.'&hash='.$hash;
        $uri = TRINITY_HOST.'/partners/user/autorizedevice?requestid='.$int.'&partnerid='.Self::PARTNERID.'&localid='.$localid.'&mac='.$mac.'&hash='.$hash;
        $res = self::_get_uri($uri);

        $js = json_decode($res);
        
        if (isset($js->requestid) && $js->requestid == $int && $js->result == 'success'){
            return array(
                'error' => 0,
                'msg'   => ''
            );
        }
        else {
            return array(
                'error' => 1,
                'msg'   => 'Something went wrong: '.$js->result
            );
        }
    }
    
    
    /*
     * Delete MAC address from subscription
     * 
     * @param int $localid Identificator of user at your net
     * @param string $mac Users MAC address at your net (00:00:00:00:00:00, 00-00-00-00-00-00, 000000000000)
     * 
     * @return array ('error'=>0/1, 'msg'=>'error message or empty')
     * 
     */
    public static function MACdel($localid=0,$mac='')
    {
        // old (till 2017.12.15) GET /partners/user/deletemac?requestid={requestid}&partnerid={partnerid}&localid={localid}&mac={mac}&hash={hash}
        // GET /partners/user/deletedevice?requestid={requestid}&partnerid={partnerid}&localid={localid}&mac={mac}&uuid={uuid}&hash={hash}
        // hash = md5(requestid+partnerid+localid+mac+salt)
        
        if ($localid == 0 || empty($localid) || !is_numeric($localid) || (int)($localid)<1){
            return array('error'=>1,'msg'=>'No local user ID entered');
        }
        
        if (
            empty($mac) || 
            !preg_match('#^\w\w[:-]?\w\w[:-]?\w\w[:-]?\w\w[:-]?\w\w[:-]?\w\w$#uis',$mac)
        ){
            return array('error'=>1,'msg'=>'No valid user MAC address entered');
        }
        
        $mac = str_replace('-','',$mac);
        $mac = str_replace(':','',$mac);
        $mac = strtoupper($mac);
        
        $int = self::GenInt();
        $hash = md5($int.self::PARTNERID.$localid.$mac.self::SALT);
        // $uri = TRINITY_HOST.'/partners/user/deletemac?requestid='.$int.'&partnerid='.Self::PARTNERID.'&localid='.$localid.'&mac='.$mac.'&hash='.$hash;
        $uri = TRINITY_HOST.'/partners/user/deletedevice?requestid='.$int.'&partnerid='.Self::PARTNERID.'&localid='.$localid.'&mac='.$mac.'&hash='.$hash;
        $res = self::_get_uri($uri);

        $js = json_decode($res);
        
        if (isset($js->requestid) && $js->requestid == $int && $js->result == 'success'){
            return array(
                'error' => 0,
                'msg'   => ''
            );
        }
        else {
            $js->result = (isset($js->result)) ? $js->result : ' no result';
            return array(
                'error' => 1,
                'msg'   => 'Something went wrong: '.$js->result
            );
        }
    }
    
    
    /*
     * Auth user by code
     * 
     * @param int $localid Identificator of user at your net
     * @param string $code Code for user auth (0000 - 9999)
     * 
     * @return array ('error'=>0/1, 'mac'=>'000000000000', 'msg'=>'error message or empty')
     * 
     */
    public static function MACcode($localid=0,$code='')
    {
        // GET /partners/user/autorizebycode?requestid={requestid}&partnerid={partnerid}&localid={localid}&code={code}&hash={hash}
        // hash = md5(requestid+partnerid+localid+code+salt)
        
        if ($localid == 0 || empty($localid) || !is_numeric($localid) || (int)($localid)<1){
            return array('error'=>1,'msg'=>'No local user ID entered');
        }
        
        if (
            empty($code) || 
            !preg_match('#^[0-9]{4}$#uis',$code)
        ){
            return array('error'=>1,'msg'=>'No valid code entered');
        }
        
        $int = self::GenInt();
        $hash = md5($int.self::PARTNERID.$localid.$code.self::SALT);
        $uri = TRINITY_HOST.'/partners/user/autorizebycode?requestid='.$int.'&partnerid='.Self::PARTNERID.'&localid='.$localid.'&code='.$code.'&hash='.$hash;
        $res = self::_get_uri($uri);
        
        $js = json_decode($res);
        
        if (isset($js->requestid) && $js->requestid == $int && $js->result == 'success'){
            return array(
                'error' => 0,
                'mac'   => $js->mac,
                'msg'   => ''
            );
        }
        else {
            $js->result = (isset($js->result)) ? $js->result : ' no result';
            return array(
                'error' => 1,
                'mac'   => '',
                'msg'   => 'Something went wrong: '.$js->result
            );
        }
    }
    
    
    /*
     * Get users list of MACs
     * 
     * @param int $localid Identificator of user at your net
     * 
     * @return array ('error'=>0/1, 'macs'=>array('444444444444','555555555555'), 'msg'=>'error message or empty')
     * 
     */
    public static function MAClist($localid=0)
    {
        // old (till 2017.12.15) GET /partners/user/listmac?requestid={requestid}&partnerid={partnerid}&localid={localid}&hash={hash}
        // GET/partners/user/devicelist?requestid={requestid}&partnerid={partnerid}&localid={localid}&hash={hash}
        // hash = md5(requestid+partnerid+localid+salt)
        
        if ($localid == 0 || empty($localid) || !is_numeric($localid) || (int)($localid)<1){
            return array('error'=>1,'msg'=>'No local user ID entered');
        }
        
        $int = self::GenInt();
        $hash = md5($int.self::PARTNERID.$localid.self::SALT);
        // $uri = TRINITY_HOST.'/partners/user/listmac?requestid='.$int.'&partnerid='.Self::PARTNERID.'&localid='.$localid.'&hash='.$hash;
        $uri = TRINITY_HOST.'/partners/user/devicelist?requestid='.$int.'&partnerid='.Self::PARTNERID.'&localid='.$localid.'&hash='.$hash;
        $res = self::_get_uri($uri);
        
        $js = json_decode($res);
        
        if (isset($js->requestid) && $js->requestid == $int && $js->result == 'success')
        {
            $macs = array();
            foreach ((array)$js->devices as $d){
                if (!empty($d->mac)) $macs[] = $d->mac;
            }
            return array(
                'error' => 0,
                // 'macs'  => (array)$js->maclist,
                'macs' => $macs,
                'msg'   => ''
            );
        }
        else {
            $js->result = (isset($js->result)) ? $js->result : ' no result';
            return array(
                'error' => 1,
                'macs'  => array(),
                'msg'   => 'Something went wrong: '.$js->result
            );
        }
    }
    
    
    /*
     * Get users list of MACs
     * 
     * @return array ('error'=>0/1, users => array(12 => array('subscrid' => 123, 'subscrprice' => 0, 'subscrstatusid' => 0)), 'msg'=>'error message or empty')
     * 
     */
    public static function UserList()
    {
        // GET /partners/user/subscriberlist?requestid={requestid}&partnerid={partnerid}&hash={hash}
        // hash = md5(requestid+partnerid+salt)
        
        $int = self::GenInt();
        $hash = md5($int.self::PARTNERID.self::SALT);
        $uri = TRINITY_HOST.'/partners/user/subscriberlist?requestid='.$int.'&partnerid='.Self::PARTNERID.'&hash='.$hash;
        $res = self::_get_uri($uri);
        
        $js = json_decode($res);
        
        if (isset($js->requestid) && $js->requestid == $int && $js->result == 'success'){
            return array(
                'error' => 0,
                'users' => (array)$js->subscribers,
                'msg'   => ''
            );
        }
        else {
            $js->result = (isset($js->result)) ? $js->result : ' no result';
            return array(
                'error' => 1,
                'users' => array(),
                'msg'   => 'Something went wrong: '.$js->result
            );
        }
    }
    
    
    /*
     *
     * Generate Unique number
     * 
     * @return int
     * 
     */
    private static function GenInt()
    {
        list($usec, $sec) = explode(' ', microtime());
        
        return str_replace('.','',((float)$sec.(float)$usec));
    }
    
    private static function _get_uri($uri='')
    {
        if (empty($uri)){
            return array('error'=>1,'msg'=>'No URI');
        }
        
        $c = curl_init();
        //curl_setopt($c, CURLOPT_VERBOSE, 1);
        curl_setopt($c, CURLOPT_HEADER, 0);
        curl_setopt($c, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($c, CURLOPT_REFERER, '');
        curl_setopt($c, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($c, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/55.0.2883.87 Safari/537.36');
        curl_setopt($c, CURLOPT_URL, $uri);
        $rC = curl_exec($c);
        curl_close($c);
        
        return $rC;
    }
    
}

