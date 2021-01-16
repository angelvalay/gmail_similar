<template>
  <div>
    <!-- botones para marcar y eliminar -->
    <div v-if="this.selected">
      <md-button class="md-primary" @click="sendToInboxAPI"><md-icon>restore_from_trash</md-icon> restore mail(s) ({{this.selected}})</md-button>
      <md-button class="md-accent" @click="onClickDeleteForeverEmails"><md-icon>delete_forever</md-icon> delete forever ({{this.selected}})</md-button>
    </div>
    <!-- fin de los botones-->
    <md-list class="md-triple-line">
      <md-progress-bar v-if="!this.loaded" class="md-accent" md-mode="indeterminate"></md-progress-bar>

      <md-empty-state
          v-if="!mails.length && this.loaded"
          class="md-primary"
          md-icon="delete_outline"
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
            <span>{{ mail.mail_to ? 'me' : mail.mail_from }} <span class="md-caption">({{translateDate(mail.created_at)}})</span></span>
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
import moment from 'moment';
export default {
name: "TrashComponent",
  data: () => ({
    mails:[],
    showConfirmMessageDelete:false,
    loaded:false,
    showSentInboxSuccessful:false,
    showSuccessfulMessageDelete:false
  }),
  computed:{
    selected() {
      return this.mails.filter(x=>x.is_selected).length;
    }
  },
  mounted() {
    this.getEmailsTrashAPI();
  },
  methods:{
    onCancelConfirmDelete(){
      this.showConfirmMessageDelete = false;
    },
    onConfirmDelete(){
      this.showConfirmMessageDelete = false;
      this.deleteMailsSelected();
    },
    onClickDeleteForeverEmails(){
      this.showConfirmMessageDelete = true;
    },
    deleteMailsSelected(){
      let data = {
        id_mails:[]
      };
      this.mails.filter(mail=>mail.is_selected).forEach(mail=>{
        data.id_mails.push(mail.id);
      });
      this.axios.put('http://localhost:8000/mails/delete',data).then(()=>{

        this.showSuccessfulMessageDelete = true;

        let lastIndexSelected = 0;
        while(lastIndexSelected > -1){
          lastIndexSelected = this.mails.findIndex(mail=>mail.is_selected);
          if (lastIndexSelected === -1)
            break;
          this.mails.splice(lastIndexSelected,1);
        }
      }).catch(error=>{
        console.log(error);
      }).then(()=>{

      });
    },
    getEmailsTrashAPI(){
      this.loaded = false;
      this.axios.get('http://localhost:8000/mails/trash').then((response)=>{
        let data = response.data;
        for (let i = 0; i < data.length; i++) {
          data[i].is_selected = false;
          let mail = data[i].mail_to ? 'me' : data[i].mail_from;
          data[i].first_letter = mail.substring(0,1).toUpperCase();
        }
        this.mails = data;
      }).catch((error)=>{
        console.log(error);
      }).then(()=>{
        this.loaded = true;
      })
    },
    sendToInboxAPI(){
      let data = {
        id_mails:[],
        is_deleted:false
      };
      this.mails.filter(mail=>mail.is_selected).forEach(mail=>{
        data.id_mails.push(mail.id);
      });
      this.axios.put('http://localhost:8000/mails/trash',data).then(()=>{
        this.showSentInboxSuccessful = true;
        let lastIndexSelected = 0;
        while(lastIndexSelected > -1){
          lastIndexSelected = this.mails.findIndex(mail=>mail.is_selected);
          if (lastIndexSelected === -1)
            break;
          this.mails.splice(lastIndexSelected,1);
        }
      }).catch(error=>{
        console.log(error);
      }).then(()=>{

      });
    },
    translateDate(date){
      return moment(date).fromNow();
    }
  }
}
</script>

<style scoped>
.md-avatar-icon{
  margin-right: 15px !important;

}
</style>