<template>
  <div>
    <!-- botones para marcar y eliminar -->
    <div v-if="this.selected">
      <md-button class="md-primary"><md-icon>grade</md-icon> mark as important ({{this.selected}})</md-button>
      <md-button class="md-accent"><md-icon>delete</md-icon> move to trash ({{this.selected}})</md-button>
    </div>
    <!-- fin de los botones-->
    <md-list class="md-triple-line">
      <md-progress-bar v-if="!this.loaded" class="md-accent" md-mode="indeterminate"></md-progress-bar>

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
            <span>{{ mail.mail_to }}</span>
            <span>{{ mail.title }}</span>
            <p>{{ mail.body }}</p>
          </div>
          <md-button class="md-icon-button md-list-action" v-on:click="mail.is_important = !mail.is_important">
            <md-icon v-bind:class="{'md-primary':mail.is_important}">
              {{ mail.is_important ? 'star':'star_border' }}
            </md-icon>
          </md-button>
        </md-list-item>
        <md-divider class="md-inset"></md-divider>
      </div>
      <!-- fin de los correos-->
    </md-list>
  </div>
</template>

<script>
export default {
name: "InboxComponent",
  data: () => ({
    mails:[],
    loaded:false
  }),
  mounted() {
    this.getAllEmailsAPI();
  },
  computed:{
    selected() {
      return this.mails.filter(x=>x.is_selected).length;
    }
  },
  methods:{
    setEmail(newEmail){
      this.mails.push(newEmail);
    },
    getAllEmailsAPI(){
      this.loaded = false;
      this.axios.get('http://localhost:8000/mails').then((response)=>{
        let data = response.data;
        for (let i = 0; i < data.length; i++) {
          data[i].is_selected = false;
          data[i].first_letter = data[i].mail_to.substring(0,1).toUpperCase();
        }
        this.mails = data;
      }).catch((error)=>{
        console.log(error);
      }).then(()=>{
        this.loaded = true;
      })
    }
  }
}
</script>

<style scoped>
  .md-avatar-icon{
    margin-right: 15px !important;

  }
</style>