<template>
  <div>
    <!-- botones para marcar y eliminar -->
    <div v-if="this.selected">
      <md-button class="md-primary" @click="sendToInboxAPI"><md-icon>move_to_inbox</md-icon> move to inbox ({{this.selected}})</md-button>
      <!-- todo falta agregar un boton para eliminar definitivamente los correos-->
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

        </md-list-item>
        <md-divider class="md-inset"></md-divider>
      </div>
      <!-- fin de los correos-->
    </md-list>
  </div>
</template>

<script>
export default {
name: "TrashComponent",
  data: () => ({
    mails:[],
    loaded:false
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
    getEmailsTrashAPI(){
      this.loaded = false;
      this.axios.get('http://localhost:8000/mails/trash').then((response)=>{
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
        // todo -> falta mostrar un mensaje de exito
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
    }
  }
}
</script>

<style scoped>
.md-avatar-icon{
  margin-right: 15px !important;

}
</style>