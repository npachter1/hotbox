<template>
  <li ref="dropdown" class="dropdown dropdown-notifications" v-bind:class="{ 'open':isDropdownOpen}" style="display: inline-block;z-index: 99999;">
     <a href="#" class="dropdown-toggle" v-on:click.prevent="toggleTheDropdown">
      <i :data-count="total" class="hotbox-icon hotbox-icon-f-comment notification-icon" :class="{ 'hide-count': !hasUnread }" />
    </a>

    <div class="dropdown-container">
      <div class="dropdown-toolbar">
        <div v-show="hasUnread" class="dropdown-toolbar-actions">
          <a href="#" @click.prevent="markAllRead" class="notification-link">Mark all as read</a>
        </div>

        <h3 class="dropdown-toolbar-title" style="color: #2c2c2c;">
          Notifications ({{ total }})
        </h3>
      </div>

      <ul class="dropdown-menu inner">
        <notification v-for="notification in notifications"
                      :key="notification.id"
                      :notification="notification"
                      @read="markAsRead(notification)"
        />

        <li v-if="!hasUnread" class="notification">
          You don't have any unread notifications.
        </li>
      </ul>

      <div v-if="hasUnread" class="dropdown-footer text-center">
          <a href="#" @click.prevent="fetch(0)" class="notification-link">View All</a>
      </div>
    </div>
  </li>
</template>

<script>

import axios from 'axios'
import Notification from './Notification'

export default {
  components: { Notification },

  props: {
    userId: String
  },

data: () => ({
    total: 0,
    notifications: [],
    loading: false,
    isPushEnabled: false,
    pushButtonDisabled: true,
    isDropdownOpen: false
  }),

  computed: {
    hasUnread () {
      return this.total > 0
    }
  },

  mounted () {
    if (window.Echo) {
      this.registerServiceWorker()
    }
    this.fetch()

    if (window.Echo) {
      this.listen()
    }
  },

  methods: {

    documentClick(e){
        if (!this.$el.contains(event.target)) {
          this.isDropdownOpen = false;
        }
    },
    registerServiceWorker () {
      if (!('serviceWorker' in navigator)) {
        console.log('Service workers aren\'t supported in this browser.')
        return
      }
      navigator.serviceWorker.register(window.location.origin + `/sw.js`)
        .then(() => this.initialiseServiceWorker())
    },
    initialiseServiceWorker () {
      if (!('showNotification' in ServiceWorkerRegistration.prototype)) {
        console.log('Notifications aren\'t supported.')
        return
      }
      if (Notification.permission === 'denied') {
        console.log('The user has blocked notifications.')
        return
      }
      if (!('PushManager' in window)) {
        console.log('Push messaging isn\'t supported.')
        return
      }
      navigator.serviceWorker.ready.then(registration => {
        registration.pushManager.getSubscription()
          .then(subscription => {
            this.pushButtonDisabled = false
            if (!subscription) {
              this.subscribe();
              return
            }
            this.updateSubscription(subscription)
            this.isPushEnabled = true
          })
          .catch(e => {
            console.log('Error during getSubscription()', e)
          })
      })
    },
    /**
     * Subscribe for push notifications.
     */
    subscribe () {
      navigator.serviceWorker.ready.then(registration => {
        const options = { userVisibleOnly: true }
        const vapidPublicKey = process.env.MIX_VAPID_PUBLIC_KEY;
        if (vapidPublicKey) {
          options.applicationServerKey = this.urlBase64ToUint8Array(vapidPublicKey)
        }
        registration.pushManager.subscribe(options)
          .then(subscription => {
            this.isPushEnabled = true
            this.pushButtonDisabled = false
            this.updateSubscription(subscription)
          })
          .catch(e => {
            if (Notification.permission === 'denied') {
              console.log('Permission for Notifications was denied')
              this.pushButtonDisabled = true
            } else {
              console.log('Unable to subscribe to push.', e)
              this.pushButtonDisabled = false
            }
          })
      })
    },
    /**
     * Unsubscribe from push notifications.
     */
    unsubscribe () {
      navigator.serviceWorker.ready.then(registration => {
        registration.pushManager.getSubscription().then(subscription => {
          if (!subscription) {
            this.isPushEnabled = false
            this.pushButtonDisabled = false
            return
          }
          subscription.unsubscribe().then(() => {
            this.deleteSubscription(subscription)
            this.isPushEnabled = false
            this.pushButtonDisabled = false
          }).catch(e => {
            console.log('Unsubscription error: ', e)
            this.pushButtonDisabled = false
          })
        }).catch(e => {
          console.log('Error thrown while unsubscribing.', e)
        })
      })
    },
    /**
     * Toggle push notifications subscription.
     */
    togglePush () {
      if (this.isPushEnabled) {
        this.unsubscribe()
      } else {
        this.subscribe()
      }
    },
    /**
     * Send a request to the server to update user's subscription.
     *
     * @param {PushSubscription} subscription
     */
    updateSubscription (subscription) {
      const key = subscription.getKey('p256dh')
      const token = subscription.getKey('auth')
      const contentEncoding = (PushManager.supportedContentEncodings || ['aesgcm'])[0]
      const data = {
        endpoint: subscription.endpoint,
        publicKey: key ? btoa(String.fromCharCode.apply(null, new Uint8Array(key))) : null,
        authToken: token ? btoa(String.fromCharCode.apply(null, new Uint8Array(token))) : null,
        contentEncoding
      }
      this.loading = true
      axios.post('/subscriptions', data)
        .then(() => { this.loading = false })
    },
    /**
     * Send a requst to the server to delete user's subscription.
     *
     * @param {PushSubscription} subscription
     */
    deleteSubscription (subscription) {
      this.loading = true
      axios.post('/subscriptions/delete', { endpoint: subscription.endpoint })
        .then(() => { this.loading = false })
    },
    /**
     * Send a request to the server for a push notification.
     */
    sendNotification () {
      this.loading = true
      axios.post('/notifications')
        .catch(error => console.log(error))
        .then(() => { this.loading = false })
    },
    /**
     * https://github.com/Minishlink/physbook/blob/02a0d5d7ca0d5d2cc6d308a3a9b81244c63b3f14/app/Resources/public/js/app.js#L177
     *
     * @param  {String} base64String
     * @return {Uint8Array}
     */
    urlBase64ToUint8Array (base64String) {
      const padding = '='.repeat((4 - base64String.length % 4) % 4)
      const base64 = (base64String + padding)
        .replace(/\-/g, '+')
        .replace(/_/g, '/')
      const rawData = window.atob(base64)
      const outputArray = new Uint8Array(rawData.length)
      for (let i = 0; i < rawData.length; ++i) {
        outputArray[i] = rawData.charCodeAt(i)
      }
      return outputArray
    },


    /**
     * Fetch notifications.
     *
     * @param {Number} limit
     */
    fetch (limit = 5) {
      axios.get('/notifications', { params: { limit } })
        .then(({ data: { total, notifications } }) => {
          this.total = total
          this.notifications = notifications.map(({ id, data, created }) => {
            return {
              id: id,
              title: data.title,
              body: data.body,
              created: created,
              action_url: data.action_url
            }
          })
        })
    },

    /**
     * Mark the given notification as read.
     *
     * @param {Object} notification
     */
    markAsRead ({ id }) {
      const index = this.notifications.findIndex(n => n.id === id)

      if (index > -1) {
        this.total--
        this.notifications.splice(index, 1)
        axios.patch(`/notifications/${id}/read`)
      }
    },

    /**
     * Mark all notifications as read.
     */
    markAllRead () {
      this.total = 0
      this.notifications = []

      axios.post('/notifications/mark-all-read')
    },

    /**
     * Listen for Echo push notifications.
     */
    listen () {
      Echo.private(`App.User.`+ this.userId)
        .notification(notification => {
          this.total++
          this.notifications.unshift(notification)
        })
        .listen('NotificationRead', ({ notificationId }) => {
          this.total--

          const index = this.notifications.findIndex(n => n.id === notificationId)
          if (index > -1) {
            this.notifications.splice(index, 1)
          }
        })
        .listen('NotificationReadAll', () => {
          this.total = 0
          this.notifications = []
        });

    },

    /**
     * Toggle the notifications dropdown.
     */
    toggleTheDropdown () {
      this.isDropdownOpen = !this.isDropdownOpen;
    }
  },
  created () {
      document.addEventListener('click', this.documentClick)
    },
  destroyed () {
    document.removeEventListener('click', this.documentClick)
  }
}
</script>
<style>

li.dropdown.dropdown-notifications.open > a.dropdown-toggle::after
{
  transform: rotate(180deg);
}

.dropdown-container a {
  color:#f96332;
}

.dropdown-container {
    right: 0;
    left: auto;
    padding-left: 1px;
    padding-right: 1px;
}

.dropdown-menu:before {
    right: 10px;
    left: auto;
}

</style>


