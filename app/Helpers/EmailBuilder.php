<?php

namespace App\Helpers;

use App\Exceptions\CustomErrorException;
use Illuminate\Support\Facades\Log;
use InvalidArgumentException;

/**
 * Class for build raw email message.
 * based on https://github.com/D9ping/BrachioMailer
 */
class EmailBuilder
{
    public const MIME_VERSION = '1.0';
    public const MAIL_LINE_MAX_LENGTH_HEADER = 998;
    public const MAIL_LINE_MAX_LENGTH_BODY = 998;
    public const CONV_MAIL_MAX_LINKS = 32;
    public const BOUNDARY_PREFIX = '--';
    public const END_PART_SUFFIX = '--';
    private array $attachments = [];
    private string $messageCharset = 'UTF-8';
    private string $messageContentType = 'text/html';
    private ?string $replyTo = null;
    private string $mailFrom = '';
    private string $nameFrom = '';
    private string $mailTo = '';
    private string $subject = '';
    private string $emailBody = '';

    private array $mailCC = [];


    public function __construct()
    {
        if (!defined('CHR_ENTER')) {
            define('CHR_ENTER', "\r\n");
        }
    }

    /**
     * RFC2045, RFC2046, RFC2047, RFC4288, RFC4289 and RFC2049 MIME content type.
     *
     * @param string $messageContentType The message content-type e.g. this can be: "text/plain" or "text/html"
     */
    public function setMessageContentType(string $messageContentType): void
    {
        $this->messageContentType = $messageContentType;
    }


    /**
     * The e-mail address to reply to.
     * If this is set (not null or empty string) it will be used otherwise Reply-to: header is not included.
     *
     * @param string $replyTo The reply to e-mail address to send a reaction to this message to.
     */
    public function setReplyTo(string $replyTo): void
    {
        if (
            !filter_var($replyTo, FILTER_VALIDATE_EMAIL) ||
            str_contains($replyTo, ' ') ||
            str_contains($replyTo, "\r") ||
            str_contains($replyTo, "\n")
        ) {
            throw new InvalidArgumentException(sprintf('The %1$s value is not a valid e-mail address.', '$replyTo'));
        }

        $this->replyTo = $replyTo;
    }

    public function setMailFrom(string $mailFrom): void
    {
        if (empty($mailFrom)) {
            return;
        }
        if (
            !filter_var($mailFrom, FILTER_VALIDATE_EMAIL) ||
            str_contains($mailFrom, ' ') ||
            str_contains($mailFrom, "\r") ||
            str_contains($mailFrom, "\n")
        ) {
            return;
        }

        $this->mailFrom = $mailFrom;
    }


    public function setMailTo(string $mailTo): void
    {
        if (
            !filter_var($mailTo, FILTER_VALIDATE_EMAIL) ||
            str_contains($mailTo, ' ') ||
            str_contains($mailTo, "\r") ||
            str_contains($mailTo, "\n")
        ) {
            throw new InvalidArgumentException(sprintf('The %1$s value is not a valid e-mail address.', '$mailTo'));
        }

        $this->mailTo = $mailTo;
    }

    /**
     * @param string $subject
     * @return void
     * @throws CustomErrorException
     */
    public function setSubject(string $subject): void
    {
        if (strlen($subject) > 200) {
            throw new CustomErrorException('Subject may not be more than 200 characters.', 422);
        }
        $this->subject = $subject;
    }

    public function setEmailBody(string $emailBody): void
    {
        $this->emailBody = $emailBody;
    }

    public function setNameFrom(string $nameFrom): void
    {
        if ($this->isValidName($nameFrom)) {
            $this->nameFrom = $nameFrom;
        }
    }


    public function setMailCC(array $mailCC): void
    {
        foreach ($mailCC as $mail) {
            if (
                !filter_var($mail, FILTER_VALIDATE_EMAIL) ||
                str_contains($mail, ' ') ||
                str_contains($mail, "\r") ||
                str_contains($mail, "\n")
            ) {
                throw new InvalidArgumentException(sprintf('The %1$s value is not a valid e-mail address.', '$mail'));
            }
        }
        $this->mailCC = $mailCC;
    }

    /**
     * Override the magic __debugInfo method (new in PHP 5.6.0) because
     * if the method isn't defined on an object, then ALL public, protected and private properties could be shown.
     */
    public function __debugInfo()
    {
        return array('error' => '__debugInfo disabled.');
    }

    /**
     * Override the magic __toString method.
     */
    public function __toString()
    {
        return '__toString disabled.';
    }

    /**
     * @return string
     * @throws CustomErrorException
     */
    public function prepareEmail(): string
    {
        if (empty($this->mailTo)) {
            throw new CustomErrorException('EmailTo missed', 422);
        }


        if (empty($this->subject)) {
            throw new CustomErrorException('Subject missed', 422);
        }

        if (empty($this->emailBody)) {
            throw new CustomErrorException('Message body missed', 422);
        }


        if (empty($this->messageCharset)) {
            $this->messageCharset = 'UTF-8';
        }

        $headers = '';
        if (!empty($this->mailFrom)) {
            $this->addHeaderLine('Return-Path', $this->mailFrom, $headers);
            if (empty($this->nameFrom)) {
                $this->addHeaderLine('From', $this->mailFrom, $headers);
            } else {
                if (preg_match("/^[a-zA-Z0-9\s\.\-\'\\\\,\/]+$/", $this->nameFrom)) {
                    // Only allow: a-z, A-Z, 0-9, space, dot, comma, dash, single quote, slash and backslash.
                    $this->addHeaderLine('From', $this->nameFrom . ' <' . $this->mailFrom . '>', $headers);
                } else {
                    mb_internal_encoding('UTF-8');
                    $encFromName = mb_encode_mimeheader($this->nameFrom, 'UTF-8', 'Q') . ' <' . $this->mailFrom . '>';
                    $this->addHeaderLine('From', $encFromName, $headers);
                }
            }
        }


        if (!empty($this->replyTo)) {
            $this->addHeaderLine('Reply-To', $this->replyTo, $headers);
        }


        $headers .= 'MIME-Version: ' . self::MIME_VERSION . CHR_ENTER;


        $this->addHeaderLine('Auto-Submitted', 'auto-generated', $headers);


        $this->addHeaderLine('X-Mailer', 'PHP/' . PHP_MAJOR_VERSION . '.' . PHP_MINOR_VERSION, $headers);

        $multiPartAlternative = '';
        $orgMessage = $this->emailBody;
        $body = '';
        $numAttachments = count($this->attachments);
        if ($numAttachments >= 1) {
            // Has attachments
            //$this->addHeaderLine('X-MS-Has-Attach', 'Yes', $headers);
            $multipartMixed = $this->generateBoundary('');

            $headers .= 'Content-Type: multipart/mixed;' . CHR_ENTER;
            $headers .= "\t" . 'boundary="' . $multipartMixed . '"' . CHR_ENTER; // line folding


            $body .= self::BOUNDARY_PREFIX . $multipartMixed . CHR_ENTER;
            if ($this->messageContentType === 'text/html') {
                $multiPartAlternative = $this->generateBoundary($multipartMixed);
                $body .= 'Content-Type: multipart/alternative;' . CHR_ENTER;
                $body .= "\t" . 'boundary="' . $multiPartAlternative . '"' . CHR_ENTER; // line folding
                $body .= 'Content-Transfer-Encoding: quoted-printable' . CHR_ENTER;
                $body .= CHR_ENTER;
                $body .= self::BOUNDARY_PREFIX . $multiPartAlternative . CHR_ENTER;
            }

            // add plaintext content part
            $this->addHeaderLine(
                'Content-Type',
                'text/plain; charset=' . $this->messageCharset,
                $body,
                self::MAIL_LINE_MAX_LENGTH_BODY,
            );
            $body .= 'Content-Transfer-Encoding: quoted-printable' . CHR_ENTER;
            $body .= CHR_ENTER;
            if ($this->messageContentType === 'text/html') {
                // add text/plain fallback from text/html part
                $body .= quoted_printable_encode($this->convertHtmlToText($orgMessage)) . CHR_ENTER;

                // add text/html part
                $body .= self::BOUNDARY_PREFIX . $multiPartAlternative . CHR_ENTER;
                $this->addHeaderLine(
                    'Content-Type',
                    $this->messageContentType . '; charset=' . $this->messageCharset,
                    $body,
                    self::MAIL_LINE_MAX_LENGTH_BODY,
                );
                $body .= 'Content-Transfer-Encoding: quoted-printable' . CHR_ENTER;
                $body .= CHR_ENTER;
            }
            $body .= quoted_printable_encode($orgMessage) . CHR_ENTER;


            $body .= CHR_ENTER;

            // Add attachments
            $attachments = $this->attachments;
            foreach ($attachments as $attachmentName => $attachment) {
                $binaryFileContent = file_get_contents($attachment['file']);
                if ($binaryFileContent === false) {
                    Log::error(sprintf('Could not read %s.', $attachment['file']));
                    continue;
                }

                $body .= self::BOUNDARY_PREFIX . $multipartMixed . CHR_ENTER;
                $encodedAttachmentName = $attachmentName;
                if (!preg_match("/^[a-zA-Z0-9\s\.\-\'\\\\,\/]+$/", $attachmentName)) {
                    $encodedAttachmentName = mb_encode_mimeheader($attachmentName, 'UTF-8', 'Q');
                }

                $this->addHeaderLine(
                    'Content-Type',
                    $attachment['mime'] . '; name="' . $encodedAttachmentName . '"',
                    $body,
                    self::MAIL_LINE_MAX_LENGTH_BODY,
                );

                // make file description shorter.
                $headerKeyMime = 'Content-Type';
                if (strlen($attachment['description']) > 253 - strlen($headerKeyMime)) {
                    $this->addHeaderLine(
                        $headerKeyMime,
                        substr($attachment['description'], 0, 253 - strlen($headerKeyMime)),
                        $body,
                        self::MAIL_LINE_MAX_LENGTH_BODY,
                    );
                } else {
                    $this->addHeaderLine(
                        'Content-Description',
                        $attachment['description'],
                        $body,
                        self::MAIL_LINE_MAX_LENGTH_BODY,
                    );
                }

                $body .= 'Content-Transfer-Encoding: base64' . CHR_ENTER;
                $body .= 'Content-Disposition: attachment;' . CHR_ENTER;
                $body .= "\t" . 'filename="' . $encodedAttachmentName . '"; size=' .
                    $attachment['size'] . ';' . CHR_ENTER;  // line folding
                $body .= CHR_ENTER;
                $body .= chunk_split(base64_encode($binaryFileContent));
            }

            $body .= self::BOUNDARY_PREFIX . $multipartMixed . self::END_PART_SUFFIX . CHR_ENTER;
        } else {
            // No attachments.
            //$this->addHeaderLine('X-MS-Has-Attach', 'No', $headers);
            if ($this->messageContentType === 'text/html') {
                $multiPartAlternative = $this->generateBoundary('');
            }


            if ($this->messageContentType === 'text/html') {
                // Create fallback so use multipart/alternative in header.
                $headers .= 'Content-Type: multipart/alternative;' . CHR_ENTER;
                $headers .= "\t" . 'boundary="' . $multiPartAlternative . '"' . CHR_ENTER; // line folding
            } else {
                $this->addHeaderLine(
                    'Content-Type',
                    $this->messageContentType . '; charset=' . $this->messageCharset,
                    $headers,
                );
            }


            if ($this->messageContentType === 'text/html') {
                // add text/plain fallback
                $body .= self::BOUNDARY_PREFIX . $multiPartAlternative . CHR_ENTER;
                $this->addHeaderLine(
                    'Content-Type',
                    'text/plain; charset=' . $this->messageCharset,
                    $body,
                    self::MAIL_LINE_MAX_LENGTH_BODY,
                );
                $body .= 'Content-Transfer-Encoding: quoted-printable' . CHR_ENTER;
                $body .= CHR_ENTER;
                $body .= quoted_printable_encode($this->convertHtmlToText($orgMessage)) . CHR_ENTER;

                // add text/html part
                $body .= self::BOUNDARY_PREFIX . $multiPartAlternative . CHR_ENTER;
                $this->addHeaderLine(
                    'Content-Type',
                    $this->messageContentType . '; charset=' . $this->messageCharset,
                    $body,
                    self::MAIL_LINE_MAX_LENGTH_BODY,
                );
                $body .= 'Content-Transfer-Encoding: quoted-printable' . CHR_ENTER;
                $body .= CHR_ENTER;
            }

            $body .= quoted_printable_encode($orgMessage) . CHR_ENTER;
        }

        $headers .= 'Content-Transfer-Encoding: quoted-printable' . CHR_ENTER;


        $mail = $headers;
        $this->addHeaderLine('To', $this->mailTo, $mail);
        foreach ($this->mailCC as $mailCC) {
            $this->addHeaderLine('CC', $mailCC, $mail);
        }
        $this->addHeaderLine('Subject', $this->subject, $mail);
        $mail .= CHR_ENTER;
        $mail .= $body;

        return $mail;
    }


    /**
     * Return file size
     * @param string $path Path of the file
     * @return false|int File size(as string) or false if error
     */
    private function realFileSize(string $path): bool|int
    {
        $size = filesize($path);
        if (!($file = fopen($path, 'rb'))) {
            return false;
        }

        if ($size >= 0) {
            if (fseek($file, 0, SEEK_END) === 0) {
                fclose($file);

                return $size;
            }
        }

        return false;
    }

    /**
     * Add a attachment to a e-mail.
     *
     * @param string $file The file path to the file to include in the message.
     * @param string $attachmentName The filename of the attachment in the message.
     * @param string $mimetype The MIME type of the attachment e.g. application/pdf or image/png etc.
     * @param string $description The description text of the attachment. Not used by all mail clients.
     * @return void
     * @throws CustomErrorException
     */
    public function addAttachment(
        string $file,
        string $attachmentName,
        string $mimetype,
        string $description = '',
    ): void {
        if (empty($mimetype)) {
            throw new CustomErrorException('Mime type for attachment not given.', 422);
        }

        if (!file_exists($file)) {
            throw new CustomErrorException('Uploaded attachment doesnt exist.', 422);
        }

        if (empty($description) && !empty($attachmentName)) {
            $description = $attachmentName;
        }

        if (strlen($description) > self::MAIL_LINE_MAX_LENGTH_BODY) {
            throw new CustomErrorException('Description too long.');
        }

        $size = $this->realFileSize($file);
        if (!preg_match("/^[a-zA-Z0-9\s\.\-\'\\\\,\/]+$/", $description)) {
            $description = mb_encode_mimeheader($description, 'UTF-8', 'Q');
        }

        $this->attachments[$attachmentName] = array(
            'file' => $file,
            'mime' => $mimetype,
            'description' => $description,
            'size' => $size,
        );
    }

    /**
     * Add a header to headers string. Checks for illegal characters and line length.
     *
     * @param string $property
     * @param string $value
     * @param string $headers The reference to the headers to add a new line to if valid.
     * @param int $lineLenLimit
     */
    private function addHeaderLine(
        string $property,
        string $value,
        string &$headers,
        int $lineLenLimit = self::MAIL_LINE_MAX_LENGTH_HEADER,
    ): void {
        if (str_contains($value, ':')) {
            Log::error('Error header value contains illegal ":" character.');

            return;
        }

        if (str_contains($value, "\r") || str_contains($value, "\n")) {
            Log::error('Error header value contains illegal enter character(s).');

            return;
        }

        $line = $property . ': ' . $value . CHR_ENTER;
        if (strlen($line) < $lineLenLimit) {
            $headers .= $line;
        } else {
            Log::error(sprintf('Header %1$s exceeded %2$d characters.', $property, $lineLenLimit));
        }
    }

    /**
     * Convert a html message to text message without html tags.
     *
     * @param string $htmlMessage
     * @return string Text message without html tags.
     */
    private function convertHtmlToText(string $htmlMessage): string
    {
        $posStartBody = stripos($htmlMessage, '<body');
        if ($posStartBody !== false) {
            // Only use body of html document.
            $htmlMessage = substr($htmlMessage, $posStartBody);
        }


        $countLinks = 0;
        $lenStartAnchor = strlen('<a ');
        $lenEndAnchor = strlen('</a>');
        $posStartAnchor = strrpos($htmlMessage, '<a ');
        $posCloseAnchor = strpos($htmlMessage, '</a>', $posStartAnchor + $lenStartAnchor);
        while ($posStartAnchor !== false && $posCloseAnchor !== false && $countLinks < self::CONV_MAIL_MAX_LINKS) {
            ++$countLinks;
            $posEndOpenAnchor = strpos($htmlMessage, '>', $posStartAnchor);
            // Get the anchor attribute
            $anchorAttrs = substr(
                $htmlMessage,
                $posStartAnchor + $lenStartAnchor,
                $posEndOpenAnchor - $posStartAnchor - $lenStartAnchor,
            );
            // find the link href attribute.
            $posStartLinkAttr = strpos($anchorAttrs, 'href="') + 6;
            $posEndLinkAttr = strpos($anchorAttrs, '"', $posStartLinkAttr);
            $link = substr($anchorAttrs, $posStartLinkAttr, $posEndLinkAttr - $posStartLinkAttr);
            $link = rawurldecode($link);
            if (filter_var($link, FILTER_VALIDATE_URL)) {
                // insert the link after the close anchor tag
                $posAfterEndAnchor = $posCloseAnchor + $lenEndAnchor;
                $preHtmlMessage = substr($htmlMessage, 0, $posAfterEndAnchor);
                $postHtmlMessage = substr($htmlMessage, $posAfterEndAnchor);
                $htmlMessage = $preHtmlMessage . '[' . $link . ']' . $postHtmlMessage;
            }

            // find new anchor
            $posFromEnd = -(strlen($htmlMessage) - $posStartAnchor) - 1;
            $posStartAnchor = strrpos($htmlMessage, '<a ', $posFromEnd);
            $posCloseAnchor = strpos($htmlMessage, '</a>', $posStartAnchor);
        }

        // Replace HTML <br> tags with enter characters.
        $htmlMessage = str_ireplace(array('<br>', '<br />', '<br/>'), CHR_ENTER, $htmlMessage);
        // Replace HTML <em> and <b> tags with * characters.
        $htmlMessage = str_ireplace(
            array('<em>', '</em>', '<b>', '</b>', '<h1>', '</h1>', '<h2>', '</h2>', '<h3>', '</h3>'),
            '*',
            $htmlMessage,
        );
        // Replace HTML <u> tags with _ characters.
        $htmlMessage = str_ireplace(array('<u>', '</u>'), '_', $htmlMessage);
        // Replace HTML <i> tags with / characters.
        $htmlMessage = str_ireplace(array('<i>', '</i>'), '/', $htmlMessage);
        // Now strip all html tags..
        $htmlMessage = strip_tags($htmlMessage);
        // Remove html ignored space and tabs and for plaintext.
        $htmlMessage = str_replace(array('  ', "\t"), '', $htmlMessage);

        // Replace &nbsp; with space.
        return str_ireplace('&nbsp;', ' ', $htmlMessage);
    }

    /**
     * Generate a boundary for multipart messages fast.
     * There are no requirement for the need for a secure pseudo random boundary value at all.
     *
     * @param string $previousBoundary
     * @param int $lenBoundary Shorter boundary means slightly shorter message but higher change of collisions.
     * @return string The new boundary. It should never be the same as previous generated boundaries for current mail.
     */
    private function generateBoundary(string $previousBoundary, int $lenBoundary = 32): string
    {
        if ($lenBoundary <= 0) {
            return '';
        }

        $lenMessageDigest = null;
        $newBoundary = '';
        $remainingLenBoundary = $lenBoundary;
        while ($remainingLenBoundary > 0) {
            $newBoundaryPart = hash('crc32b', mt_rand(0, PHP_INT_MAX));
            if (is_null($lenMessageDigest)) {
                $lenMessageDigest = strlen($newBoundaryPart);
            }

            if ($remainingLenBoundary < $lenMessageDigest) {
                $newBoundaryPart = substr($newBoundaryPart, 0, $remainingLenBoundary);
            }

            $newBoundary .= strtoupper($newBoundaryPart);
            $remainingLenBoundary -= $lenMessageDigest;
        }

        if ($newBoundary === $previousBoundary) {
            $newBoundary = $this->generateBoundary($previousBoundary, $lenBoundary);
        }

        return $newBoundary;
    }

    /**
     * Check if from/to name does not contain illegal characters.
     *
     * @param string $fromName
     * @return bool
     */
    private function isValidName(string $fromName): bool
    {
        if (str_contains($fromName, '<') || str_contains($fromName, '>')) {
            return false;
        }

        return true;
    }
}
