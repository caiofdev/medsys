import { faCalendar } from "@fortawesome/free-solid-svg-icons";
import { FontAwesomeIcon } from "@fortawesome/react-fontawesome";
import LocalDateTime from "../local-date-time";

type DashboardHeaderProps = {
    userName: string;
};

export default function DashboardHeader({ userName }: DashboardHeaderProps) {
    return(
        <div className="flex flex-row col-span-2 overflow-hidden h-fit justify-between text-darktext rounded-radius mb-4">
            <div className='flex flex-col gap-2 justify-between h-full'>
                <div className="flex flex-row items-center text-md w-[fit-content] p-1 pr-2 pl-2 bg-gray-200  text-gray-800 rounded-radius mb-6">
                    <FontAwesomeIcon icon={faCalendar} className="mr-2"/>
                    <LocalDateTime />
                </div>
                <div>
                    <div className=" text-3xl font-bold p-0 m-0">
                        Bem Vindo, {userName}!
                    </div>
                    <div className=" text-md p-0 m-0 font-light">
                        Aqui está um resumo do sistema hoje.
                    </div>
                </div>
            </div>
        </div>
    );
}