/*
Script: 		enterfrom(top|right|bottom|left) + leavefrom(top|right|bottom|left) Events
Version:       	1
License:		MIT-style license.
Credits:		Connectjs Colorz - www.colorz.fr 
				Mootools 1.3 compatible			
				Mootools framework - mootools.net.
*/
prev_m_x = 0;
prev_m_y = 0;

window.addEvent('mousemove',function(e){
        
    prev_m_x=e.page.x;
    prev_m_y=e.page.y;
            
          
         
         
});

Element.Events.enterfromleft ={
    base: 'mouseover',
    condition: function(event){
        var elpos = this.getPosition();  
        if(prev_m_x<=elpos.x)  return true;
        return;   
    }
}

Element.Events.enterfromright ={
    base: 'mouseover',
    condition: function(event){
        var elpos = this.getPosition();  
        if(prev_m_x>=(elpos.x+this.getWidth())) return true;
        return;   
    }
}

Element.Events.enterfromtop ={
    base: 'mouseover',
    condition: function(event){
        var elpos = this.getPosition();  
        if(prev_m_x>=elpos.x && prev_m_y<=elpos.y)  return true;
        return;   
    }
}

Element.Events.enterfrombottom ={
    base: 'mouseover',
    condition: function(event){
        var elpos = this.getPosition();  
        if(prev_m_x>=elpos.x && prev_m_y>=(elpos.y+this.getHeight()))  return true;
        return;   
    }
}

/************ leave ************/

Element.Events.leavefromleft ={
    base: 'mouseout',
    condition: function(event){
       
        prev_m_x=event.page.x;
        prev_m_y=event.page.y;
        var elpos = this.getPosition();  
        if(prev_m_x<=elpos.x)  return true;
        return;   
    }
}

Element.Events.leavefromright ={
    base: 'mouseout',
    condition: function(event){
        prev_m_x=event.page.x;
        prev_m_y=event.page.y;
        var elpos = this.getPosition();  
        if(prev_m_x>=(elpos.x+this.getWidth())) return true;
        return;   
    }
}

Element.Events.leavefromtop ={
    base: 'mouseout',
    condition: function(event){
        prev_m_x=event.page.x;
        prev_m_y=event.page.y;
        var elpos = this.getPosition();  
        if(prev_m_x>=elpos.x && prev_m_y<=elpos.y)  return true;
        return;   
    }
}

Element.Events.leavefrombottom ={
    base: 'mouseout',
    condition: function(event){
        prev_m_x=event.page.x;
        prev_m_y=event.page.y;
        var elpos = this.getPosition();  
        if(prev_m_x>=elpos.x && prev_m_y>=(elpos.y+this.getHeight()))  return true;
        return;   
    }
}


