import { Avatar } from "@radix-ui/react-avatar";
import { AvatarFallback, AvatarImage } from "../ui/avatar";
import { useInitials } from "@/hooks/use-initials";

type DashboardProfileProps = {
    userName: string,
    imgPath: string,
    type: string
};

export default function DashboardProfile( { userName, imgPath, type}: DashboardProfileProps ) {
    const getInitials = useInitials();
    return (
        <div className="flex flex-col col-span-1 overflow-hidden rounded-xl border border-sidebar-border/70 dark:border-sidebar-border bg-primary" style={{ backgroundColor: '#F7F2EB' }}>
            <div className='flex flex-col h-full'>
                <div className='flex p-2 pl-3 rounded-xl' style={{ backgroundColor: '#030D29' }}>
                    <p className='text-xl text-white font-bold'>Meu Perfil</p>
                </div>
                <div className='flex flex-row justify-start items-center h-full'>
                    <div className='flex p-5 w-fit'>
                        <Avatar className="h-22 w-22 rounded-full border-2 border-[#9FA3AE]">
                            <AvatarImage 
                                src={imgPath} 
                                alt={userName} 
                                className="object-cover w-full h-full rounded-full"
                            />
                            <AvatarFallback className="bg-[#9fa3ae63] text-2xl">
                                {getInitials(userName)}
                            </AvatarFallback>
                        </Avatar>
                    </div>
                    <div className='flex flex-col w-fit justify-self-start gap-0'>
                        <p className='font-bold text-xl'>{userName}</p>
                        <p>{type}</p>
                    </div>
                </div>
            </div>
        </div>
    );
}