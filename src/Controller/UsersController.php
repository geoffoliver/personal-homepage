<?php
namespace App\Controller;

use App\Controller\AppController;

use Cake\Utility\Hash;
/**
 * Users Controller
 *
 * @property \App\Model\Table\UsersTable $Users
 *
 * @method \App\Model\Entity\User[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class UsersController extends AppController
{
    // valid response types
    private $responseTypes = ['id', 'code'];

    public function initialize()
    {
        parent::initialize();
        $this->Authentication->allowUnauthenticated([
            'login',
            'indieAuth',
            // 'resetPassword'
        ]);
    }

    public function login()
    {
        $result = $this->Authentication->getResult();
        $session = $this->getRequest()->getSession();
        $attempts = $session->read('loginAttempts', 0);
        $lastAttempt = $session->read('lastAttempt');

        if (
            $lastAttempt &&
            $lastAttempt >= strtotime('-5 minutes') &&
            $attempts >= 5
        ) {
            // user tried to login too many times and failed
            $this->Flash->error(__('Too many failed login attempts.'));
            return $this->redirect('/');
        }

        // regardless of POST or GET, redirect if user is logged in
        if ($result->isValid()) {
            $session->delete('loginAttempts');
            $redirect = $this->request->getQuery(
                'redirect',
                ['controller' => 'Homepage', 'action' => 'feed']
            );
            return $this->redirect($redirect);
        }

        if ($this->request->is(['post']) && !$result->isValid()) {
            $session->write('loginAttempts', $attempts + 1);
            $session->write('lastAttempt', time());
            $this->Flash->error(__('Invalid username or password'));
        }
    }

    public function logout()
    {
        $this->request->allowMethod(['post']);

        $this->Authentication->logout();
        $this->Flash->success(__('You have been logged out'));
        return $this->redirect([
            'controller' => 'Homepage',
            'action' => 'index'
        ]);
    }

    /*
    public function resetPassword($hash = null)
    {
        if (!$hash) {
            throw new \Exception(__('Invalid request'));
        }

        $user = $this->Users->find()
            ->where([
                'Users.reset_hash' => $hash
            ])
            ->first();

        if (!$user) {
            $this->Flash->error(__('Invalid request'));
            return $this->redirect([
                'controller' => 'Users',
                'action' => 'login'
            ]);
        }

        if ($this->request->is('post')) {
            $password = $this->request->getData('password');
            if (!$password) {
                throw new \Exception('Missing password');
            }

            $user->password = $password;
            $user->reset_hash = null;
            if ($this->Users->save($user)) {
                $this->Flash->success(__('Password reset successfully'));
                return $this->redirect([
                    'controller' => 'Users',
                    'action' => 'login'
                ]);
            } else {
                $this->Flash->error(__('Unable to reset password'));
            }
        }

        $this->set([
            'user' => $user
        ]);
    }
    */

    /**
     * IndieAuth login so you can login to other websites with your own website!
     *
     * A _lot_ of this code is taken directly from
     * https://github.com/Inklings-io/selfauth/blob/master/index.php
     * and I thank them for all their hard work.
     */
    public function indieAuth()
    {
        $request = $this->getRequest();

        if ($request->is('get')) {
            return $this->indieAuthLogin($request);
        }

        if ($request->is('post')) {
            return $this->indieAuthVerify($request);
        }
    }

    private function indieAuthLogin($request)
    {
        // get the stuff we need from the URL. thank
        $me = filter_var($request->getQuery('me'), FILTER_VALIDATE_URL);
        $clientId = filter_var($request->getQuery('client_id'), FILTER_VALIDATE_URL);
        $redirectUri = filter_var($request->getQuery('redirect_uri'), FILTER_VALIDATE_URL);
        $state = filter_var_regexp($request->getQuery('state'), '@^[\x20-\x7E]*$@');
        $responseType = filter_var_regexp($request->getQuery('response_type'), '@^(id|code)?$@');
        $scope = filter_var_regexp($request->getQuery('scope'), '@^([\x21\x23-\x5B\x5D-\x7E]+( [\x21\x23-\x5B\x5D-\x7E]+)*)?$@');

        // make sure we've got all the stuff we actually need to proceed
        if (!$clientId) {
            throw new \Exception(__('Missing/invalid "client_id" field'));
        }

        if (!$redirectUri) {
            throw new \Exception(__('Missing/invalid "redirect_uri" field'));
        }

        if (!$state) {
            throw new \Exception(__('Missing/Invalid "state" field'));
        }

        if (!$me) {
            throw new \Exception(__('Missing/invalid "me" field'));
        }

        // there wasn't a response type specified, so we'll default to 'id'
        if (!$request->getQuery('response_type')) {
            $responseType = 'id';
        }

        // make sure the response type, if present, is supported
        if ($responseType && !in_array($responseType, $this->responseTypes)) {
            throw new \Exception(__('Missing/Invalid "response_type" field'));
        }

        // scopes aren't supported on 'id' response t ypes
        if ($responseType === 'id' && $scope) {
            throw new \Exception(__('The "scope" field cannot be used with identification'));
        } elseif ($responseType === 'code' && !$scope) {
            throw new \Exception(__('Missing/invalid "scope" field'));
        }

        // finally, we can show the page that prompts the user to accept
        // permissions and login
        $this->set([
            'me' => $me,
            'clientId' => $clientId,
            'redirectUri' => $redirectUri,
            'state' => $state,
            'responseType' => $responseType,
            'scopes' => $scope ? explode(' ', $scope) : []
        ]);
    }

    private function indieAuthVerify($request)
    {
        // try to get a 'code' out of the URL
        $code = filter_var_regexp($request->getData('code'), '@^[0-9a-f]+:[0-9a-f]{64}:@');

        if ($code) {
            // we have a code, so we should verify the code
            return $this->indieAuthVerifyCode($request, $code);
        }

        // we're still here, so we're just logging in
        return $this->indieAuthAuthenticate($request);
    }

    // TODO: implement h-card display - https://indieweb.org/h-card
    private function indieAuthAuthenticate($request)
    {
        $result = $this->Authentication->getResult();
        $session = $request->getSession();
        $attempts = $session->read('loginAttempts', 0);
        $lastAttempt = $session->read('lastAttempt');

        if (
            $lastAttempt &&
            $lastAttempt >= strtotime('-5 minutes') &&
            $attempts >= 5
        ) {
            // user tried to login too many times and failed
            $this->Flash->error(__('Too many failed login attempts.'));
            return $this->redirect('/');
        }

        // login failed, redirect
        if ($this->request->is('post') && !$result->isValid()) {
            $session->write('loginAttempts', $attempts + 1);
            $session->write('lastAttempt', time());
            $this->Flash->error(implode(', ', array_values(Hash::flatten($result->getErrors()))));
            return $this->redirect($this->referer());
        }

        // regardless of POST or GET, redirect if user is logged in
        if ($result->isValid()) {
            // do a bit of validation
            $me = filter_var($request->getData('me'), FILTER_VALIDATE_URL);
            $scope = filter_var_regexp($request->getData('scopes'), '@^[\x21\x23-\x5B\x5D-\x7E]+$@', FILTER_REQUIRE_ARRAY);
            $redirectUri = filter_var($request->getData('redirect_uri'), FILTER_VALIDATE_URL);
            $clientId = filter_var($request->getData('client_id'), FILTER_VALIDATE_URL);
            $state = filter_var_regexp($request->getData('state'), '@^[\x20-\x7E]*$@');
            $responseType = filter_var_regexp($request->getData('response_type'), '@^(id|code)?$@');

            if (!$me) {
                throw new \Exception(__('Missing/invalid "me" field'));
            }

            // make sure the response type, if present, is supported
            if ($responseType && !in_array($responseType, $this->responseTypes)) {
                throw new \Exception(__('Missing/Invalid "response_type" field'));
            }

            // scopes aren't supported on 'id' response t ypes
            if ($responseType === 'id' && $scope) {
                throw new \Exception(__('The "scope" field cannot be used with identification'));
            } elseif ($responseType === 'code' && !$scope) {
                throw new \Exception(__('Missing/invalid "scope" field'));
            }

            if (!$redirectUri) {
                throw new \Exception(__('Missing/invalid "redirect_uri" field'));
            }

            if ($scope) {
                // convert scope back into a spaced string
                $scope = trim(implode(' ', $scope));
            } else {
                $scope = '';
            }

            // create a code that we'll verify later
            $code = create_signed_code($me . $redirectUri . $clientId, 5 * 60, $scope);

            // build a URL where we'll redirect the user
            $redir = $redirectUri;

            if (strpos($redir, '?') === false) {
                $redir .= '?';
            } else {
                $redir .= '&';
            }

            $params = [
                'code' => $code,
                'me' => $me
            ];

            if ($state) {
                $params['state'] = $state;
            }

            $redir .= http_build_query($params);

            // clean up any login attempts
            $session->delete('loginAttempts');

            // send the user on their way
            return $this->redirect($redir);
        }

    }

    private function indieAuthVerifyCode($request, $code)
    {
        // pull some bits ouf of the post so we can verify the code
        $redirectUri = filter_var($request->getData('redirect_uri'), FILTER_VALIDATE_URL);
        $clientId = filter_var($request->getData('client_id'), FILTER_VALIDATE_URL);
        $me = $this->getMeUrl();

        // try to verify the code
        if (!(is_string($code)
            && is_string($redirectUri)
            && is_string($clientId)
            && verify_signed_code($me . $redirectUri . $clientId, $code))
        ) {
            throw new \Exception('Invalid code');
        }

        // start to generate a response
        $response = [
            'me' => $me
        ];

        // should we include a scope in the response?
        $codeParts = explode(':', $code, 3);
        if ($codeParts[2] !== '') {
            $response['scope'] = base64_url_decode($codeParts[2]);
        }

        // figure out how we should respond to this request
        if ($request->accepts('application/json')) {
            // send a nice json response back
            HEADER('Content-Type: application/json');
            echo json_encode($response);
            exit();
        }

        if ($request->accepts('application/x-www-form-urlencoded')) {
            // send a url encoded string back
            HEADER('Content-Type: application/x-www-form-urlencoded');
            echo http_build_query($response);
            exit();
        }

        // throw a big nasty exception because srsly, wtf?
        throw new \Exception(__('The client will not accept JSON or form encoded responses.'));
    }

    // TODO: implement token endpoint
    // - https://indieweb.org/token-endpoint
    // - https://indieweb.org/obtaining-an-access-token
    // - https://indieweb.org/scope
    public function indieToken()
    {
        die(__('Not implemented'));
    }

    private function getMeUrl()
    {
        $proto = 'http';

        if (env('SERVER_PORT') == 443) {
            $proto .= 's';
        }

        return $proto . '://' . env('SERVER_NAME') . '/';
    }

}
