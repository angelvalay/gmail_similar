<template>
  <div>
    <md-list class="md-triple-line">
      <md-progress-bar v-if="!loaded" class="md-accent" md-mode="indeterminate"></md-progress-bar>

      <md-empty-state
          v-if="!mails.length && loaded"
          class="md-primary"
          md-icon="trash"
          md-label="Nothing in Trash"
          md-description="The trash is empty.">
      </md-empty-state>

      <!-- lista de correos -->
      <div v-for="(mail,index) in mails" :key="index">
        <md-list-item>
          <md-button class="md-icon-button" v-on:click="mail.is_selected = !mail.is_selected">
            <md-icon>
              {{mail.is_selected ? 'check_box':'check_box_outline_blank'}}
            </md-icon>
          </md-button>

          <md-avatar class="md-avatar-icon">{{ mail.first_letter }}</md-avatar>

          <div class="md-list-item-text">
            <span>{{ mail.mail_to ? 'me' : mail.mail_from }} <span class="md-caption">({{mail.created_from_now}})</span></span>
            <span>{{ mail.title }}</span>
            <p>{{ mail.body }}</p>
          </div>

        </md-list-item>
        <md-divider class="md-inset"></md-divider>
      </div>
      <!-- fin de los correos-->
    </md-list>
    <md-snackbar :md-position="'left'" :md-duration="4000" :md-active.sync="showSentInboxSuccessful" md-persistent>
      <span>Email(s) restored!</span>
    </md-snackbar>
    <md-snackbar :md-position="'left'" :md-duration="4000" :md-active.sync="showSuccessfulMessageDelete" md-persistent>
      <span>Email(s) deleted permanently!</span>
    </md-snackbar>
    <md-dialog-confirm
        :md-active.sync="showConfirmMessageDelete"
        md-title="Are you sure?"
        md-content="The selected emails will be permanently deleted and you will not be able to recover it."
        md-confirm-text="Yes"
        md-cancel-text="No"
        @md-cancel="onCancelConfirmDelete"
        @md-confirm="onConfirmDelete" />
  </div>
</template>

<script>
export default {
name: "TrashComponent",
  props:['mails','selected', 'loaded'],
  data: () => ({
    showConfirmMessageDelete:false,
    showSentInboxSuccessful:false,
    showSuccessfulMessageDelete:false
  }),
  methods:{
    onCancelConfirmDelete(){
      this.showConfirmMessageDelete = false;
    },
    onConfirmDelete(){
      this.showConfirmMessageDelete = false;
      this.deleteMailsSelectedAPI();
    },
    onClickDeleteForeverEmails(){
      this.showConfirmMessageDelete = true;
    },
    deleteMailsSelectedAPI(){
      console.log('delete mails selected');
    },
    sendToInboxAPI(){
      console.log('send to inbox');
    },
  }
}
</script>

<style scoped>
.md-avatar-icon{
  margin-right: 15px !important;

}
</style>