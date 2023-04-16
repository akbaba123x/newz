<?php
/**
 * @class socketMail
 * @brief Send email through socket
 *
 * @package Clearbricks
 * @subpackage Mail
 *
 * @copyright Olivier Meunier & Association Dotclear
 * @copyright GPL-2.0-only
 */
class socketMail
{
    /**
     * Socket handle
     *
     * @var        resource|null|false
     */
    public static $fp;

    /**
     * Connection timeout (in seconds)
     *
     * @var        int
     */
    public static $timeout = 10;

    /**
     * SMTP Relay to user
     *
     * @var        string
     */
    public static $smtp_relay = null;

    /**
     * Send email through socket
     *
     * This static method sends an email through a simple socket connection.
     * If {@link $smtp_relay} is set, it will be used as a relay to send the
     * email. Instead, email is sent directly to MX host of domain.
     *
     * @param string            $to             Email destination
     * @param string            $subject        Email subject
     * @param string            $message        Email message
     * @param string|array      $headers        Email headers
     *
     * @throws Exception
     */
    public static function mail(string $to, string $subject, string $message, $headers = null): void
    {
        if (!is_null($headers) && !is_array($headers)) {
            $headers = [$headers];
        }
        $from = self::getFrom($headers);

        $from_host = explode('@', $from);
        $from_host = $from_host[1];

        $to_host = explode('@', $to);
        $to_host = $to_host[1];

        if (self::$smtp_relay != null) {
            $mx = [gethostbyname(self::$smtp_relay) => 1];
        } else {
            $mx = mail::getMX($to_host);
        }

        foreach (array_keys($mx) as $mx_host) {
            self::$fp = @fsockopen($mx_host, 25, $errno, $errstr, self::$timeout);

            if (self::$fp !== false) {
                break;
            }
        }

        if (!is_resource(self::$fp)) {
            self::$fp = null;

            throw new Exception('Unable to open socket');
        }

        # We need to read the first line
        fgets(self::$fp);

        $data = '';
        # HELO cmd
        if (!self::cmd('HELO ' . $from_host, $data)) {
            self::quit();

            throw new Exception($data);
        }

        # MAIL FROM: <...>
        if (!self::cmd('MAIL FROM: <' . $from . '>', $data)) {
            self::quit();

            throw new Exception($data);
        }

        # RCPT TO: <...>
        if (!self::cmd('RCPT TO: <' . $to . '>', $data)) {
            self::quit();

            throw new Exception($data);
        }

        # Compose mail and send it with DATA
        $buffer = 'Return-Path: <' . $from . ">\r\n" .
            'To: <' . $to . ">\r\n" .
            'Subject: ' . $subject . "\r\n";

        foreach ($headers as $header) {
            $buffer .= $header . "\r\n";
        }

        $buffer .= "\r\n\r\n" . $message;

        if (!self::sendMessage($buffer, $data)) {
            self::quit();

            throw new Exception($data);
        }

        self::quit();
    }

    /**
     * Gets the from.
     *
     * @param      array      $headers  The headers
     *
     * @throws     Exception
     *
     * @return     string     The from.
     */
    private static function getFrom(?array $headers): string
    {
        if (!is_null($headers)) {
            // Try to find a from:… in header(s)
            foreach ($headers as $header) {
                $from = '';

                if (preg_match('/^from: (.+?)$/msi', $header, $m)) {
                    $from = trim((string) $m[1]);
                }

                if (preg_match('/(?:<)(.+?)(?:$|>)/si', $from, $m)) {
                    $from = trim((string) $m[1]);
                } elseif (preg_match('/^(.+?)\(/si', $from, $m)) {
                    $from = trim((string) $m[1]);
                } elseif (!text::isEmail($from)) {
                    $from = '';
                }

                if ($from !== '') {
                    return $from;
                }
            }
        }

        // Is a from set in configuration options ?
        $from = trim((string) ini_get('sendmail_from'));
        if ($from !== '') {
            return $from;
        }

        throw new Exception('No valid from e-mail address');
    }

    /**
     * Send SMTP command
     *
     * @param      string  $out    The out
     * @param      string  $data   The received data
     *
     * @return     bool
     */
    private static function cmd(string $out, string &$data = ''): bool
    {
        fwrite(self::$fp, $out . "\r\n");
        $data = self::data();

        if (substr($data, 0, 3) != '250') {
            return false;
        }

        return true;
    }

    /**
     * Get data from opened stream
     *
     * @return     string
     */
    private static function data(): string
    {
        $buffer = '';
        stream_set_timeout(self::$fp, 2);

        for ($i = 0; $i < 2; $i++) {
            $buffer .= fgets(self::$fp, 1024);
        }

        return $buffer;
    }

    /**
     * Sends a message body.
     *
     * @param      string  $msg    The message
     * @param      string  $data   The data
     *
     * @return     bool
     */
    private static function sendMessage(string $msg, string &$data): bool
    {
        $msg .= "\r\n.";

        self::cmd('DATA', $data);

        if (substr($data, 0, 3) != '354') {
            return false;
        }

        return self::cmd($msg, $data);
    }

    /**
     * Send QUIT command and close socket handle
     */
    private static function quit(): void
    {
        self::cmd('QUIT');
        fclose(self::$fp);
        self::$fp = null;
    }
}
