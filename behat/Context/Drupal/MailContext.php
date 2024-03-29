<?php

namespace Drupal\Behat\Context\Drupal;

use Alex\MailCatcher\Behat\MailCatcherAwareInterface;
use Alex\MailCatcher\Behat\MailCatcherTrait;
use Alex\MailCatcher\Message;
use Behat\Behat\Context\Context;
use Behat\Mink\Exception\ElementNotFoundException;
use Drupal\DrupalExtension\Context\RawDrupalContext;

/**
 * Defines mails application features from the specific context.
 */
class MailContext extends RawDrupalContext implements Context, MailCatcherAwareInterface {
  use MailCatcherTrait;

  /**
   * The base URL.
   *
   * @var string
   */
  protected $baseUrl;

  /**
   * MailCatcher current message.
   *
   * @var \Alex\MailCatcher\Message|null
   */
  protected $currentMessage;

  /**
   * Initializes context.
   *
   * Every scenario gets its own context instance.
   * You can also pass arbitrary arguments to the
   * context constructor through behat.yml.
   */
  public function __construct($base_url) {
    $this->baseUrl = $base_url;
  }

  /**
   * Verify if mail has been sent with parameters to and subject.
   *
   * @param string $to
   *   The mail receiver.
   * @param string $subject
   *   The mail subject.
   *
   * @throws \Exception
   *
   * @Then A mail as been sent to :to with subject :subject
   */
  public function aMailAsBeenSentToWithSubject($to, $subject) {
    $message = $this->getMailCatcherClient()->searchOne([
      Message::TO_CRITERIA => $to,
      Message::SUBJECT_CRITERIA => $subject,
    ]);

    if (empty($message)) {
      throw new \Exception(sprintf("No mail to '%s' with subject '%s' was found on the inbox", $to, $subject));
    }
  }

  /**
   * Verify if mail has been sent with parameters to, subject and attachments.
   *
   * @param string $to
   *   The mail receiver.
   * @param string $subject
   *   The mail subject.
   *
   * @throws \Exception
   *
   * @Then A mail as been sent to :to with subject :subject with attachments
   */
  public function aMailAsBeenSentToWithSubjectAndAttachments($to, $subject) {
    $message = $this->getMailCatcherClient()->searchOne([
      Message::TO_CRITERIA => $to,
      Message::SUBJECT_CRITERIA => $subject,
      Message::ATTACHMENTS_CRITERIA => TRUE,
    ]);

    if (empty($message)) {
      throw new \Exception(sprintf("No mail to '%s' with subject '%s' and attachments was found on the inbox", $to, $subject));
    }
  }

  /**
   * Confirms if a mail was sent to a recipient with a specific subject.
   *
   * This method checks if an email was sent to the provided address ($to),
   * with the specified subject ($subject), and without any attachments.
   *
   * @param string $to
   *   The mail receiver.
   * @param string $subject
   *   The mail subject.
   *
   * @throws \Exception
   *
   * @Then A mail as been sent to :to with subject :subject with no attachments
   */
  public function aMailAsBeenSentToWithSubjectAndNoAttachments($to, $subject) {
    $message = $this->getMailCatcherClient()->searchOne([
      Message::TO_CRITERIA => $to,
      Message::SUBJECT_CRITERIA => $subject,
      Message::ATTACHMENTS_CRITERIA => FALSE,
    ]);

    if (empty($message)) {
      throw new \Exception(sprintf("No mail to '%s' with subject '%s' and NO attachments was found on the inbox", $to, $subject));
    }
  }

  /**
   * Test if the mail catcher has any mail with the corresponding subject.
   *
   * @param string $subject
   *   The mail subject.
   *
   * @throws \Exception
   *
   * @Then I should not see mail with subject :subject
   */
  public function shouldNotSeeaMailWithSubject($subject) {
    $message = $this->getMailCatcherClient()->searchOne([
      Message::SUBJECT_CRITERIA => $subject,
    ]);

    if ($message !== NULL) {
      throw new \Exception(sprintf('A mail with subject \'%s\' was fon don the inbox', $subject));
    }
  }

  /**
   * Verify if mail has been sent with parameter subject.
   *
   * @param string $subject
   *   The subject to search for.
   *
   * @throws \Exception
   *
   * @Then A mail as been sent with the subject :subject
   */
  public function aMailAsBeenSentWithSubject($subject) {
    $this->findMail(Message::SUBJECT_CRITERIA, $subject);
  }

  /**
   * Ensure the current mail has been written in the proper language code.
   *
   * To be able to check a mail langcode, you need to previously open it.
   *
   * @param string $langcode
   *   The ISO langcode.
   *
   * @throws \Exception
   *
   * @Then The current mail has been written in :langcode
   */
  public function currentMailLangcode($langcode) {
    $message = $this->getCurrentMessage();
    /** @var \Alex\MailCatcher\Mime\HeaderBag $header_bag */
    $header_bag = $message->getHeaders();

    if (!$header_bag->has('x-tests-langcode')) {
      throw new \InvalidArgumentException(sprintf('Expected "x-tests-langcode" header\'s mail to be present, got nothing.'));
    }

    if ($header_bag->get('x-tests-langcode') !== $langcode) {
      throw new \InvalidArgumentException(sprintf('Expected mail\'s langcode to be %s, got %s.', $langcode, $header_bag->get('x-tests-langcode')));
    }
  }

  /**
   * Open email and valid account.
   *
   * @param string $value
   *   The string the mail should contain.
   *
   * @throws \Exception
   *
   * @Then /^I open mail containing "([^"]+)"$/
   */
  public function iOpenMailContaining($value) {
    $message = $this->findMail(Message::CONTAINS_CRITERIA, $value);
    $this->currentMessage = $message;
  }

  /**
   * Open a mail with a given subject.
   *
   * @When /^I open mail with subject "([^"]+)"$/
   */
  public function openMailSubject($value) {
    $message = $this->findMail(Message::SUBJECT_CRITERIA, $value);
    $this->currentMessage = $message;
  }

  /**
   * Purge mails.
   *
   * @BeforeScenario @mail
   * @AfterScenario @mail
   */
  public function purge() {
    $this->getMailCatcherClient()->purge();
  }

  /**
   * Verify a given link it visible with a given href attr in the mail.
   *
   * @Then I should see link with href :href in mail
   *
   * @throws \Exception
   * @throws \Behat\Mink\Exception\ElementNotFoundException
   */
  public function seeLinkInMail($href) {
    $message = $this->getCurrentMessage();

    $dom = new \DOMDocument();
    $dom->loadHTML($message->getContent());
    $xpath = new \DOMXPath($dom);

    $entries = $xpath->query("//a[contains(@href, '{$href}')]");

    if ($entries->length === 0) {
      throw new ElementNotFoundException($this->getSession(), 'link', 'href', $href);
    }
  }

  /**
   * Assert we don't found a mail from a given email adresse.
   *
   * @Then /^I should not see mail from "([^"]+)"$/
   */
  public function shouldNotSeeMailFrom($value) {
    $message = $this->getMailCatcherClient()->searchOne([Message::FROM_CRITERIA => $value]);

    if (!empty($message)) {
      throw new \Exception(sprintf("A mail from '%s' was found on the inbox", $value));
    }
  }

  /**
   * Ensure a given number of mail have been catched.
   *
   * @param int $count
   *   The expected number of mail(s).
   *
   * @Then /^(?P<count>\d+) mails? should be sent$/
   */
  public function verifyMailsSent($count) {
    $count = (int) $count;
    $actual = $this->getMailCatcherClient()->getMessageCount();

    if ($count !== $actual) {
      throw new \InvalidArgumentException(sprintf('Expected %d mails to be sent, got %d.', $count, $actual));
    }
  }

  /**
   * {@inheritdoc}
   */
  private function getCurrentMessage() {
    if ($this->currentMessage === NULL) {
      throw new \RuntimeException('No message selected');
    }

    return $this->currentMessage;
  }

}
