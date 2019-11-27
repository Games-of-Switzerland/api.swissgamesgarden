module Slackistrano
  class CustomMessaging < Messaging::Base
    # Suppress updating message.
    def payload_for_updating
      nil
    end

    # Suppress reverting message.
    def payload_for_reverting
      nil
    end

    # Fancy updated message.
    # See https://api.slack.com/docs/message-attachments
    def payload_for_updated
      {
        attachments: [{
          color: 'good',
          title: 'Application deployment succeeded :boom::bangbang:',
          fields: [{
            title: 'Environment',
            value: stage,
            short: true
          }, {
            title: 'Project',
            value: application,
            short: true
          }, {
            title: 'Branch',
            value: branch,
            short: true
          }, {
            title: 'Committer',
            value: deployer,
            short: true
          }],
          fallback: super[:text]
        }]
      }
    end

    # Default reverted message.  Alternatively simply do not redefine this
    # method.
    def payload_for_reverted
      super
    end

    # Slightly tweaked failed message.
    # See https://api.slack.com/docs/message-formatting
    def payload_for_failed
      {
        attachments: [{
          color: 'danger',
          title: 'Application deployment failed :fire::hankey:',
          fields: [{
            title: 'Environment',
            value: stage,
            short: true
          }, {
            title: 'Project',
            value: application,
            short: true
          }, {
            title: 'Branch',
            value: branch,
            short: true
          }, {
            title: 'Committer',
            value: deployer,
            short: true
          }],
          fallback: super[:text]
        }]
      }
    end

    # Override the deployer helper to pull commiter name on CI or
    # the full name from the password file on local environment.
    #
    # See https://github.com/phallstrom/slackistrano/blob/master/lib/slackistrano/messaging/helpers.rb
    def deployer
      ENV['CI_COMMITTER_NAME'] || Etc.getpwnam(ENV['USER'] || ENV['USERNAME']).gecos
    rescue
      default = ENV['USER'] || ENV['USERNAME']
      fetch(:local_user, default)
    end
  end
end
