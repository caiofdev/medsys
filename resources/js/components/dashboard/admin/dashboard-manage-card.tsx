import { Link } from '@inertiajs/react';
import { Cog, ChevronDown, ChevronUp, Stethoscope, UserCheck, ShieldUser } from 'lucide-react';
import { useState } from 'react';

interface ManageCardProps {
   title: string;
   description: string;
   routes: string[];
}

export default function ManageCard({ title, description, routes }: ManageCardProps) {
   const [show, setShow] = useState(false);

   return (
      <div className="flex flex-col w-full h-full rounded-radius p-6 bg-digital-blue-50 border border-border">
            <div className='w-fit hover:rotate-45 transition-transform duration-200 mb-2 text-darktext'>
               <Cog size={30} />
            </div>
            <h3 className="text-xl font-bold text-darktext">{title}</h3>
            <p className="text-md text-gray-700 mb-2">{description}</p>
            <div className="flex items-center gap-1 text-darktext cursor-pointer" >
               {show ?
               <div className="flex flex-col gap-2">
                  <div className="flex gap-2">
                     <Link 
                        href={routes[0]}
                        title='Administradores'
                        className="p-2 bg-digital-blue-800/10 border-1 rounded-radius hover:bg-digital-blue-800/20 cursor-pointer transition-colors"
                     >
                        <ShieldUser />
                     </Link>
                     <Link 
                        href={routes[1]}
                        title='Doutores'
                        className="p-2 bg-digital-blue-800/10 border-1 rounded-radius hover:bg-digital-blue-800/20 cursor-pointer transition-colors"
                     >
                        <Stethoscope />
                     </Link>
                     <Link 
                        href={routes[2]}
                        title='Recepcionistas'
                        className="p-2 bg-digital-blue-800/10 border-1 rounded-radius hover:bg-digital-blue-800/20 cursor-pointer transition-colors"
                     >
                        <UserCheck />
                     </Link>
                  </div>
                  <ChevronUp size={30} onClick={() => setShow(!show)} /> 
               </div> 
                  : <ChevronDown size={30} onClick={() => setShow(!show)} />
               }
            </div>
      </div>
   );
}