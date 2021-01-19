<template>
  <div>
    <div class="viewport" v-if="mail">
      <md-toolbar :md-elevation="0">
        <span class="md-title">{{ mail.title }}</span>
      </md-toolbar>

      <md-list>
        <md-list-item>
          <md-avatar class="md-avatar-icon">{{ mail.first_letter }}</md-avatar>

          <div class="md-list-item-text">
            <span>{{ mail.mail_from ? mail.mail_from: 'me' }}</span>
            <span class="md-caption">To: {{ mail.mail_to ? mail.mail_to : 'me' }}</span>
          </div>

<!--          <md-button class="md-icon-button md-list-action">-->
<!--            <md-icon class="md-primary">chat_bubble</md-icon>-->
<!--          </md-button>-->
        </md-list-item>
      </md-list>
      <md-content class="md-scrollbar">
        {{mail.body}}
      </md-content>
    </div>
  </div>
</template>

<script>
export default {
  name: "ViewEmailComponent",
  data: () => ({
    mail:null,
  }),
  methods:{
    viewEmail(email){
      this.mail = email;
    }
  }
}
</script>

<style scoped>
  .md-content{
    padding: 0 24px 0 24px;
  }
</style>