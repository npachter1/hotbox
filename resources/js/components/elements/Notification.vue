<template>
  <li class="notification">
    <div class="media">
      <div class="media-left" style="padding-right:10px;">
        <div class="media-object">
          <img src="/images/logo.png">
        </div>
      </div>

      <div class="media-body">
        <a href="#" class="notification-link notification-mark-read" title="Mark as read" @click.stop="markAsRead">
          <i class="hotbox-icon hotbox-icon-e-remove" aria-hidden="true" />
        </a>

        <strong class="notification-title">
          <a :href="notification.action_url" class="notification-link">{{ notification.title }}</a>
        </strong>

        <p class="notification-desc">
          {{ notification.body }}
        </p>

        <div class="notification-meta">
          <small class="timestamp">
            <timeago :datetime="notification.created"  ></timeago>
          </small>
        </div>
      </div>
    </div>
  </li>
</template>

<script>
export default {
  props: {
    notification: {
      type: Object,
      required: true
    }
  },

  methods: {
    markAsRead () {
      this.$emit('read')
    }
  }
}
</script>
