<?php
class ControllerExtensionModuleOxyoCallback extends Controller
{
    public function send()
    {
        $this->load->language('extension/module/oxyo_callback');

        $json = [];

        if ($this->request->server['REQUEST_METHOD'] == 'POST') {
            $validation = $this->validateRequest($this->request->post);

            if (!empty($validation['errors'])) {
                $json['error'] = $validation['errors'];
            } else {
                $name = $validation['data']['name'];
                $phone = $validation['data']['phone'];

                $store_name = $this->config->get('config_name');

                $mail = new Mail($this->config->get('config_mail_engine'));
                $mail->parameter = $this->config->get('config_mail_parameter');
                $mail->smtp_hostname = $this->config->get('config_mail_smtp_hostname');
                $mail->smtp_username = $this->config->get('config_mail_smtp_username');
                $mail->smtp_password = html_entity_decode($this->config->get('config_mail_smtp_password'), ENT_QUOTES, 'UTF-8');
                $mail->smtp_port = $this->config->get('config_mail_smtp_port');
                $mail->smtp_timeout = $this->config->get('config_mail_smtp_timeout');

                $mail->setTo($this->config->get('config_email'));
                $mail->setFrom($this->config->get('config_email'));
                $mail->setSender(html_entity_decode($name, ENT_QUOTES, 'UTF-8'));
                $mail->setSubject($store_name . ' > ' . $this->language->get('heading_title') . ' - ' . $phone);

                // Use language variables for content
                $body  = $this->language->get('text_callback_name') . ': ' . $name . "\n";
                $body .= $this->language->get('text_callback_phone') . ': ' . $phone;

                $mail->setText($body);
                $mail->send();

                $json['success'] = $this->language->get('text_success_form');
            }
        }

        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
    }

    protected function validateRequest($post)
    {
        $this->load->language('extension/module/oxyo_callback');

        $errors = [];
        $data = [];

        $name = trim($post['callback_name'] ?? '');
        $phone = trim($post['callback_phone'] ?? '');
        $agreement = isset($post['callback_agreement']);

        $surname = trim($post['callback_surname'] ?? '');
        if (!empty($surname)) {
            $errors['surname'] = $this->language->get('text_callback_spam_success');
            return [
                'errors' => $errors,
                'data'   => $data
            ];
        }

        if (utf8_strlen($name) < 2 || utf8_strlen($name) > 30) {
            $errors['name'] = $this->language->get('error_name');
        } else {
            $data['name'] = $name;
        }

        if (!preg_match('/^\+?[0-9\s\-\(\)]+$/', $phone)) {
            $errors['phone'] = $this->language->get('error_phone');
        } else {
            $data['phone'] = $phone;
        }

        if (!$agreement) {
            $errors['agreement'] = $this->language->get('error_agreement');
        }

        return [
            'errors' => $errors,
            'data'   => $data
        ];
    }
}
