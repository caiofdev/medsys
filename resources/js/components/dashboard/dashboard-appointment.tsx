import { FontAwesomeIcon } from "@fortawesome/react-fontawesome";
import {faCircle} from '@fortawesome/free-solid-svg-icons'

type DashboardAppointmentProps = {
    time: string;
    title: string;
    color: string;
};

export default function DashboardAppointment({time, title, color}: DashboardAppointmentProps) {
    return(
        <div className="flex flex-row items-center p-2 gap-2" style={{backgroundColor: '#F7F2EB'}}>
            <span>{time}</span>
            <FontAwesomeIcon icon={faCircle} style={{color:`#${color}`, fontSize:'9px'}}/>
            <span>{title}</span>
        </div>
    );
}   