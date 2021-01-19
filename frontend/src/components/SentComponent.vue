<template>
  <div>
    <md-list class="md-triple-line">
      <md-progress-bar v-if="!loaded" class="md-accent" md-mode="indeterminate"></md-progress-bar>

      <md-empty-state
          v-if="!mails.length && loaded"
          class="md-primary"
          md-icon="send"
          md-label="Nothing in Sent"
          md-description="Wait a moment and try again later.">
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
            <span>To: {{ mail.mail_to }} <span class="md-caption">({{mail.created_from_now}})</span></span>
            <span>{{ mail.title }}</span>
            <p>{{ mail.body }}</p>
          </div>
          <md-button class="md-icon-button md-list-action" v-on:click="markAsImportantAPI(index)">
            <md-icon v-bind:class="{'md-primary':mail.is_important}">
              {{ mail.is_important ? 'star':'star_border' }}
            </md-icon>
          </md-button>

        </md-list-item>
        <md-divider class="md-inset"></md-divider>
      </div>
      <!-- fin de los correos-->
    </md-list>
    <md-snackbar :md-position="'left'" :md-duration="4000" :md-active.sync="showMarkedSuccessful" md-persistent>
      <span>Email(s) {{markAsImportant? 'marked':'unmarked'}} as important!</span>
    </md-snackbar>
    <md-snackbar :md-position="'left'" :md-duration="4000" :md-active.sync="showTrashSuccessful" md-persistent>
      <span>Email(s) sent to trash!</span>
    </md-snackbar>
  </div>
</template>

<script>
export default {
  name: "SentComponent",
  props:['mails','selected', 'loaded'],
  data: () => ({
    showMarkedSuccessful:false,
    showTrashSuccessful:false,
    markAsImportant:false,
  }),
  methods:{
    setEmail(newEmail){
      console.log(newEmail);
      //this.mails.push(this.addExtraProperties(newEmail));
    },
    markAsImportantAPI(index = null){
      console.log(index);
    },
    sentToTrashAPI(){
      console.log('sent to trash');
    },
  }
}
</script>

<style scoped>
  .md-avatar-icon{
    margin-right: 15px !important;

  }
</style>